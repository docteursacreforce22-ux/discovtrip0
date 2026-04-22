<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    // ════════════════════════════════════════════════════════
    // DASHBOARD
    // ════════════════════════════════════════════════════════

    public function dashboard()
    {
        $user = auth()->user();

        $stats = [
            'total'     => $user->bookings()->count(),
            'upcoming'  => $user->bookings()
                ->where('booking_date', '>=', now())
                ->whereIn('status', ['confirmed', 'pending'])
                ->count(),
            'completed' => $user->bookings()
                ->where('status', 'completed')
                ->count(),
            'spent'     => $user->bookings()
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total_price'),
            'wishlist'  => $user->wishlists()->count(),
        ];

        $upcomingBookings = $user->bookings()
            ->with(['offer.city', 'tier'])
            ->where('booking_date', '>=', now())
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('booking_date')
            ->limit(5)
            ->get();

        $wishlistItems = $user->wishlists()
            ->with(['offer.city'])
            ->latest()
            ->limit(4)
            ->get();

        return view('account.dashboard', compact(
            'user', 'stats', 'upcomingBookings', 'wishlistItems'
        ));
    }

    // ════════════════════════════════════════════════════════
    // RÉSERVATIONS
    // ════════════════════════════════════════════════════════

    public function bookings(Request $request)
    {
        $user  = auth()->user();
        $query = $user->bookings()
            ->with(['offer.city', 'tier'])
            ->orderBy('booking_date', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(10);

        return view('account.bookings', compact('user', 'bookings'));
    }

    // ════════════════════════════════════════════════════════
    // PROFIL
    // ════════════════════════════════════════════════════════

    public function profile()
    {
        $user = auth()->user();
        return view('account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name'  => 'required|string|max:60',
            'last_name'   => 'required|string|max:60',
            'phone'       => 'nullable|string|max:30',
            'nationality' => 'nullable|string|max:80',
            'bio'         => 'nullable|string|max:500',
            'locale'      => 'nullable|in:fr,en',
            'birthday'    => 'nullable|date|before:today',
            'gender'      => 'nullable|in:male,female,other,prefer_not_to_say',
        ]);

        if ($request->hasFile('profile_picture')) {
            $request->validate([
                'profile_picture' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if (auth()->user()->profile_picture) {
                Storage::disk('public')->delete(auth()->user()->profile_picture);
            }

            $validated['profile_picture'] = $request
                ->file('profile_picture')
                ->store('profile-pictures', 'public');
        }

        auth()->user()->update($validated);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    // ════════════════════════════════════════════════════════
    // SÉCURITÉ
    // ════════════════════════════════════════════════════════

    public function security()
    {
        $user = auth()->user();
        return view('account.security', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:8',
        ]);

        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors([
                'current_password' => 'Mot de passe actuel incorrect.',
            ]);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }

    // ════════════════════════════════════════════════════════
    // SUPPRESSION DE COMPTE (anonymisation RGPD)
    // ════════════════════════════════════════════════════════

    public function deleteAccount(Request $request)
    {
        $user = auth()->user();

        // 1. Vérifier le mot de passe
        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'delete_password' => 'Mot de passe incorrect.',
            ])->withFragment('delete-account');
        }

        // 2. Annuler automatiquement toutes les réservations actives à venir
        $user->bookings()
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('booking_date', '>=', now())
            ->get()
            ->each(function ($booking) {
                try {
                    $booking->update(['status' => 'cancelled_by_user']);
                } catch (\Exception $e) {
                    Log::warning('Auto-cancel on account delete: booking ' . $booking->reference);
                }
            });

        // 3. Supprimer la photo de profil du storage
        if ($user->profile_picture) {
            try {
                Storage::disk('public')->delete($user->profile_picture);
            } catch (\Exception $e) {
                Log::warning('Profile picture delete failed for user ' . $user->id);
            }
        }

        // 4. Déconnecter AVANT d'anonymiser
        $userId = $user->id;
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 5. Anonymisation RGPD :
        //    - données personnelles effacées
        //    - réservations passées conservées pour l'historique admin
        //    - compte soft-deleted (deleted_at) → invisible partout
        try {
            \App\Models\User::where('id', $userId)->update([
                'first_name'      => 'Utilisateur',
                'last_name'       => 'Supprimé',
                // 'name' est un accessor calculé depuis first_name + last_name — pas une colonne DB
                'email'           => 'deleted_' . $userId . '_' . time() . '@discovtrip.com',
                'phone'           => null,
                'bio'             => null,
                'nationality'     => null,
                'birthday'        => null,
                'profile_picture' => null,
                'password'        => Hash::make(Str::random(40)),
                'remember_token'  => null,
                'deleted_at'      => now(),
            ]);

            // Favoris supprimés — pas de valeur historique
            \App\Models\Wishlist::where('user_id', $userId)->delete();

        } catch (\Exception $e) {
            Log::error('Account anonymization failed for user ' . $userId . ': ' . $e->getMessage());
            return redirect()->route('home')
                ->with('error', 'Une erreur est survenue. Contactez le support.');
        }

        return redirect()->route('home')
            ->with('success', 'Votre compte a été supprimé. Vos données personnelles ont été effacées définitivement.');
    }

    // ════════════════════════════════════════════════════════
    // FAVORIS
    // ════════════════════════════════════════════════════════

    public function wishlist()
    {
        $user          = auth()->user();
        $wishlistItems = $user
            ->wishlists()
            ->with(['offer.city', 'offer.activeTiers'])
            ->latest()
            ->paginate(12);

        return view('account.wishlist', compact('user', 'wishlistItems'));
    }
}