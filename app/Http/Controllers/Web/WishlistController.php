<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Offer;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Ajouter / retirer une offre de la wishlist (toggle)
     * POST /wishlist/toggle
     *
     * CORRECTION #9 : clé JSON 'wishlisted' (au lieu de 'in_wishlist')
     * pour correspondre à ce que lit app.js :
     *   icon.classList.toggle('fas', data.wishlisted)
     */
    public function toggle(Request $request)
    {
        $request->validate(['offer_id' => 'required|exists:offers,id']);

        $user    = auth()->user();
        $offerId = (int) $request->offer_id;

        $existing = Wishlist::where('user_id', $user->id)
                            ->where('offer_id', $offerId)
                            ->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
            $message    = 'Retiré de votre liste de souhaits.';
        } else {
            Wishlist::create([
                'user_id'  => $user->id,
                'offer_id' => $offerId,
            ]);
            $wishlisted = true;
            $message    = 'Ajouté à votre liste de souhaits ! ❤️';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'wishlisted' => $wishlisted,           // ← aligné avec app.js
                'message'    => $message,
                'count'      => $user->wishlists()->count(), // badge nav
            ]);
        }

        return back()->with($wishlisted ? 'success' : 'info', $message);
    }

    /**
     * Vue liste de souhaits
     * GET /account/wishlist
     */
    public function index()
    {
        $wishlistItems = auth()->user()
            ->wishlists()
            ->with(['offer.city', 'offer.activeTiers'])
            ->latest()
            ->paginate(12);

        return view('account.wishlist', compact('wishlistItems'));
    }
}