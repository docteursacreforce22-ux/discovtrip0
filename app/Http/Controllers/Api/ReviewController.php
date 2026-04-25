<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Services\ReviewService;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\{JsonResponse, Request};

class ReviewController
{
    public function __construct(private ReviewService $reviewService) {}

    public function store(Request $request): JsonResponse
    {
        // CORRECTION : $request->all() remplacé par validation stricte
        // Un utilisateur ne peut pas injecter status='published' ou user_id arbitraire
        $validated = $request->validate([
            'offer_id' => ['required', 'integer', 'exists:offers,id'],
            'rating'   => ['required', 'integer', 'min:1', 'max:5'],
            'title'    => ['nullable', 'string', 'max:120'],
            'body'     => ['required', 'string', 'min:10', 'max:2000'],
            // Champs INTERDITS explicitement absents :
            // status, user_id, is_featured, published_at
        ]);

        $review = $this->reviewService->createReview($request->user()->id, $validated);

        return response()->json([
            'success' => true,
            'data'    => new ReviewResource($review),
            'message' => 'Review submitted for moderation',
        ], 201);
    }

    public function offerReviews(int $offerId): JsonResponse
    {
        $reviews = $this->reviewService->getOfferReviews($offerId);

        return response()->json([
            'success' => true,
            'data'    => ReviewResource::collection($reviews),
        ]);
    }

    public function publish(int $id): JsonResponse
    {
        $review = $this->reviewService->publishReview($id);

        return response()->json([
            'success' => true,
            'data'    => new ReviewResource($review),
            'message' => 'Review published',
        ]);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        // CORRECTION : valider la raison de rejet
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->reviewService->rejectReview($id, $validated['reason'] ?? '');

        return response()->json([
            'success' => true,
            'message' => 'Review rejected',
        ]);
    }
}
