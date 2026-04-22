<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Services\ReviewService;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\{JsonResponse, Request};

class ReviewController {
    public function __construct(private ReviewService $reviewService) {}

    public function store(Request $request): JsonResponse {
        $review = $this->reviewService->createReview($request->user()->id, $request->all());
        return response()->json([
            'success' => true,
            'data' => new ReviewResource($review),
            'message' => 'Review submitted for moderation',
        ], 201);
    }

    public function offerReviews(int $offerId): JsonResponse {
        $reviews = $this->reviewService->getOfferReviews($offerId);
        return response()->json([
            'success' => true,
            'data' => ReviewResource::collection($reviews),
        ]);
    }

    public function publish(int $id): JsonResponse {
        $review = $this->reviewService->publishReview($id);
        return response()->json([
            'success' => true,
            'data' => new ReviewResource($review),
            'message' => 'Review published',
        ]);
    }

    public function reject(Request $request, int $id): JsonResponse {
        $this->reviewService->rejectReview($id, $request->input('reason', ''));
        return response()->json([
            'success' => true,
            'message' => 'Review rejected',
        ]);
    }
}
