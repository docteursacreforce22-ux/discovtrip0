<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Services\BookingService;
use App\Http\Resources\BookingResource;
use Illuminate\Http\{JsonResponse, Request};

class BookingController {
    public function __construct(private BookingService $bookingService) {}

    public function index(Request $request): JsonResponse {
        $bookings = $this->bookingService->getUserBookings($request->user()->id);
        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings),
        ]);
    }

    public function show(string $reference): JsonResponse {
        $booking = $this->bookingService->getBookingByReference($reference);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking),
        ]);
    }

    public function store(Request $request): JsonResponse {
        $booking = $this->bookingService->createBooking($request->user(), $request->all());
        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking),
        ], 201);
    }

    public function cancel(Request $request, int $id): JsonResponse {
        $booking = $this->bookingService->findById($id);
        $refund = $this->bookingService->cancelByUser(
            $booking, 
            $request->user(), 
            $request->input('reason', '')
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully',
            'data' => [
                'refund_amount' => $refund->toArray(),
            ],
        ]);
    }

    public function upcoming(Request $request): JsonResponse {
        $bookings = $this->bookingService->getUpcomingBookings($request->user()->id);
        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings),
        ]);
    }
}
