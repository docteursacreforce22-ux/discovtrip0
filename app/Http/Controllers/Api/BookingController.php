<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Services\BookingService;
use App\Http\Resources\BookingResource;
use Illuminate\Http\{JsonResponse, Request};

class BookingController
{
    public function __construct(private BookingService $bookingService) {}

    public function index(Request $request): JsonResponse
    {
        $bookings = $this->bookingService->getUserBookings($request->user()->id);

        return response()->json([
            'success' => true,
            'data'    => BookingResource::collection($bookings),
        ]);
    }

    public function show(string $reference): JsonResponse
    {
        $booking = $this->bookingService->getBookingByReference($reference);

        if (! $booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        // CORRECTION : s'assurer que l'utilisateur ne peut voir
        // que SES propres réservations via l'API
        if ($booking->user_id !== request()->user()->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json([
            'success' => true,
            'data'    => new BookingResource($booking),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // CORRECTION : $request->all() remplacé par validation stricte
        // Champs sensibles comme total_price, status, is_paid sont calculés
        // côté serveur dans BookingService — jamais acceptés du client
        $validated = $request->validate([
            'offer_id'      => ['required', 'integer', 'exists:offers,id'],
            'offer_tier_id' => ['nullable', 'integer', 'exists:offer_tiers,id'],
            'date'          => ['required', 'date', 'after_or_equal:today'],
            'time'          => ['nullable', 'string', 'max:10', 'regex:/^\d{2}:\d{2}$/'],
            'participants'  => ['required', 'integer', 'min:1', 'max:50'],
            'notes'         => ['nullable', 'string', 'max:1000'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
            // Champs INTERDITS explicitement absents :
            // total_price, status, is_paid, payment_status, reference
            // user_id → toujours pris de auth()->user()
        ]);

        $booking = $this->bookingService->createBooking($request->user(), $validated);

        return response()->json([
            'success' => true,
            'data'    => new BookingResource($booking),
        ], 201);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $booking = $this->bookingService->findById($id);

        if (! $booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        // CORRECTION : vérifier que l'utilisateur annule SA réservation
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // CORRECTION : valider la raison d'annulation
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $refund = $this->bookingService->cancelByUser(
            $booking,
            $request->user(),
            $validated['reason'] ?? ''
        );

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully',
            'data'    => [
                'refund_amount' => $refund->toArray(),
            ],
        ]);
    }

    public function upcoming(Request $request): JsonResponse
    {
        $bookings = $this->bookingService->getUpcomingBookings($request->user()->id);

        return response()->json([
            'success' => true,
            'data'    => BookingResource::collection($bookings),
        ]);
    }
}
