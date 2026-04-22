<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\BookingAdminNotificationMail;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\Offer;
use App\Models\OfferTier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    // ════════════════════════════════════
    // LISTE (utilisateur connecté)
    // ════════════════════════════════════

    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['offer.city', 'tier'])
            ->when(request('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(10);

        return view('pages.bookings.index', compact('bookings'));
    }

    // ════════════════════════════════════
    // FORMULAIRE DE RÉSERVATION
    // ════════════════════════════════════

    public function create(Request $request, string $slug)
    {
        $offer = Offer::with(['city.country', 'activeTiers'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $selectedTierId = $request->query('tier');

        // ── CORRECTION : vérification KKiaPay au lieu de FedaPay/Stripe
        $gatewayReady = ! empty(config('services.kkiapay.public_key'));
        $hasOnline    = in_array($offer->payment_mode ?? 'on_site', ['online', 'both']) && $gatewayReady;
        $hasOnSite    = in_array($offer->payment_mode ?? 'on_site', ['on_site', 'both']);

        return view('pages.bookings.create', compact(
            'offer', 'selectedTierId', 'hasOnline', 'hasOnSite',
        ));
    }

    // ════════════════════════════════════
    // CRÉATION
    // ════════════════════════════════════

    public function store(Request $request)
    {
        // ── Délai minimum de réservation depuis config
        $minHours    = config('discovtrip.booking_min_hours', 24);
        $minDateFrom = now()->addHours($minHours)->format('Y-m-d');

        $rules = [
            'offer_id'      => 'required|exists:offers,id',
            'offer_tier_id' => 'nullable|exists:offer_tiers,id',
            'date'          => "required|date|after_or_equal:{$minDateFrom}",
            'time'          => 'nullable|string|max:10',
            'participants'  => 'required|integer|min:1|max:50',
            'message'       => 'nullable|string|max:1000',
        ];

        if (! auth()->check()) {
            $rules['guest_first_name'] = 'required|string|max:60';
            $rules['guest_last_name']  = 'required|string|max:60';
            $rules['guest_email']      = 'required|email|max:120';
            $rules['guest_phone']      = 'nullable|string|max:30';
        }

        $validated = $request->validate($rules, [
            'guest_first_name.required' => 'Votre prénom est requis.',
            'guest_last_name.required'  => 'Votre nom est requis.',
            'guest_email.required'      => 'Votre email est requis pour recevoir la confirmation.',
            'guest_email.email'         => "L'adresse email n'est pas valide.",
            'date.required'             => 'Veuillez choisir une date.',
            'date.after_or_equal'       => "La date doit être au minimum {$minHours}h à partir de maintenant.",
            'participants.required'     => 'Veuillez indiquer le nombre de participants.',
        ]);

        $offer = Offer::with(['city', 'activeTiers'])->findOrFail($validated['offer_id']);

        // ── Vérification capacité participants
        if ($offer->min_participants && $validated['participants'] < $offer->min_participants) {
            return back()
                ->withErrors(['participants' => "Minimum {$offer->min_participants} participant(s) requis."])
                ->withInput();
        }
        if ($offer->max_participants && $validated['participants'] > $offer->max_participants) {
            return back()
                ->withErrors(['participants' => "Maximum {$offer->max_participants} participant(s) pour cette expérience."])
                ->withInput();
        }

        // ── Protection double réservation — même offre + date + user/email
        $emailForCheck = auth()->check()
            ? auth()->user()->email
            : ($validated['guest_email'] ?? null);

        if ($emailForCheck) {
            $duplicate = Booking::where('offer_id', $offer->id)
                ->where('booking_date', $validated['date'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($q) use ($emailForCheck) {
                    $q->whereHas('user', fn ($u) => $u->where('email', $emailForCheck))
                      ->orWhere('guest_email', $emailForCheck);
                })
                ->exists();

            if ($duplicate) {
                return back()
                    ->withErrors(['date' => 'Vous avez déjà une réservation active pour cette expérience à cette date.'])
                    ->withInput();
            }
        }

        // ── Résolution du tier et du prix
        $tier      = null;
        $unitPrice = (float) $offer->effective_price;

        if (! empty($validated['offer_tier_id'])) {
            $tier = OfferTier::where('id', $validated['offer_tier_id'])
                ->where('offer_id', $offer->id)
                ->where('is_active', true)
                ->first();
            if ($tier) {
                $unitPrice = (float) $tier->price;
            }
        }

        $totalAmount   = $unitPrice * (int) $validated['participants'];
        $paymentMode   = $offer->payment_mode ?? 'on_site';
        $chosenPayment = $request->input('payment_choice', $paymentMode);

        $status = match (true) {
            $chosenPayment === 'online'                               => 'pending',
            $paymentMode === 'on_site' && $offer->is_instant_booking => 'confirmed',
            default                                                   => 'pending',
        };

        // ── Référence unique garantie (boucle do/while)
        do {
            $reference = 'BK-' . strtoupper(Str::random(8));
        } while (Booking::where('reference', $reference)->exists());

        $bookingData = [
            'reference'     => $reference,
            'offer_id'      => $offer->id,
            'offer_tier_id' => $tier?->id,
            'booking_date'  => $validated['date'],
            'booking_time'  => $validated['time'] ?? null,
            'participants'  => (int) $validated['participants'],
            'total_price'   => $totalAmount,
            'currency'      => 'XOF',
            'status'        => $status,
            'notes'         => $validated['message'] ?? null,
        ];

        if (auth()->check()) {
            $bookingData['user_id'] = auth()->id();
        } else {
            $bookingData['user_id']          = null;
            $bookingData['guest_first_name'] = $validated['guest_first_name'];
            $bookingData['guest_last_name']  = $validated['guest_last_name'];
            $bookingData['guest_email']      = $validated['guest_email'];
            $bookingData['guest_phone']      = $validated['guest_phone'] ?? null;
        }

        // ── Transaction DB pour l'intégrité
        $booking = DB::transaction(function () use ($bookingData) {
            $booking = Booking::create($bookingData);
            $booking->load(['offer.city', 'tier']);
            return $booking;
        });

        // ── Emails HORS transaction
        $clientEmail = auth()->check()
            ? auth()->user()->email
            : ($booking->guest_email ?? null);

        if ($clientEmail) {
            try {
                Mail::to($clientEmail)->send(new BookingConfirmationMail($booking));
            } catch (\Exception $e) {
                Log::warning('BookingController: email confirmation failed: ' . $e->getMessage());
            }
        }

        try {
            $adminEmail = config('discovtrip.contact_email')
                       ?? config('mail.from.address');
            if ($adminEmail) {
                Mail::to($adminEmail)->queue(new BookingAdminNotificationMail($booking));
            }
        } catch (\Exception $e) {
            Log::warning('BookingController: admin notification failed: ' . $e->getMessage());
        }

        // ── Invalider le cache des stats home (une nouvelle réservation)
        Cache::forget('home.stats');

        $message = $offer->is_instant_booking
            ? 'Réservation confirmée ! Un email de confirmation vous a été envoyé. 🎉'
            : 'Demande envoyée ! Votre guide confirmera sous 24h. Un email vous a été envoyé.';

        // ── Redirection selon mode paiement
        if ($chosenPayment === 'online') {
            $url = (! auth()->check() && $booking->guest_email)
                ? URL::signedRoute('payment.show', ['reference' => $booking->reference])
                : route('payment.show', $booking->reference);

            return redirect($url)->with('info', 'Veuillez finaliser votre paiement pour confirmer la réservation.');
        }

        $url = (! auth()->check() && $booking->guest_email)
            ? URL::signedRoute('bookings.show', ['reference' => $booking->reference])
            : route('bookings.show', $booking->reference);

        return redirect($url)->with('success', $message);
    }

    // ════════════════════════════════════
    // DÉTAIL
    // ════════════════════════════════════

    public function show(Request $request, string $reference)
    {
        $query = Booking::where('reference', $reference)
            ->with(['offer.city', 'tier']);

        if (auth()->check()) {
            $booking = $query->where('user_id', auth()->id())->firstOrFail();
        } else {
            if (! $request->hasValidSignature()) {
                return redirect()->route('offers.index')
                    ->with('info', 'Consultez votre email de confirmation pour accéder à votre réservation.');
            }
            $booking = $query->whereNull('user_id')->firstOrFail();
        }

        return view('pages.bookings.show', compact('booking'));
    }

    // ════════════════════════════════════
    // ANNULATION
    // ════════════════════════════════════

    public function cancel(Request $request, string $reference)
    {
        $booking = Booking::where('reference', $reference)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (! in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Cette réservation ne peut pas être annulée.');
        }

        if (Carbon::parse($booking->booking_date)->isPast()) {
            return back()->with('error', "La date de l'expérience est déjà passée.");
        }

        // Vérifier la fenêtre d'annulation gratuite depuis config
        $freeHours = config('discovtrip.cancellation_free_hours', 48);
        $hoursLeft = now()->diffInHours(Carbon::parse($booking->booking_date));

        if ($hoursLeft < $freeHours) {
            return back()->with('error', "L'annulation n'est plus possible moins de {$freeHours}h avant l'expérience.");
        }

        $booking->update([
            'status'       => 'cancelled_by_user',
            'cancelled_at' => now(),
        ]);

        $booking->load(['offer.city', 'tier']);

        try {
            $cancelEmail = $booking->guest_email ?? optional(auth()->user())->email;
            if ($cancelEmail) {
                Mail::to($cancelEmail)->queue(new BookingCancelledMail($booking));
            }
        } catch (\Exception $e) {
            Log::warning('BookingController: cancel email failed: ' . $e->getMessage());
        }

        try {
            $adminEmail = config('discovtrip.contact_email')
                       ?? config('mail.from.address');
            if ($adminEmail) {
                Mail::to($adminEmail)->queue(new BookingAdminNotificationMail($booking));
            }
        } catch (\Exception $e) {
            Log::warning('BookingController: admin cancel notification failed: ' . $e->getMessage());
        }

        return redirect()
            ->route('account.bookings')
            ->with('success', 'Votre réservation a été annulée. Un email de confirmation vous a été envoyé.');
    }

    // ════════════════════════════════════
    // PDF
    // ════════════════════════════════════

    public function pdf(Request $request, string $reference)
    {
        $query = Booking::where('reference', $reference)
            ->with(['offer.city', 'tier']);

        if (auth()->check()) {
            $booking = $query->where('user_id', auth()->id())->firstOrFail();
        } else {
            if (! $request->hasValidSignature()) {
                return redirect()->route('offers.index')
                    ->with('info', 'Consultez votre email de confirmation pour accéder à votre bon.');
            }
            $booking = $query->whereNull('user_id')->firstOrFail();
        }

        return view('pages.bookings.pdf', compact('booking'));
    }
}