<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Services\OfferService;
use App\Http\Resources\OfferResource;
use Illuminate\Http\{JsonResponse, Request};

class OfferController {
    public function __construct(private OfferService $offerService) {}

    public function index(Request $request): JsonResponse {
        $offers = $this->offerService->searchOffers($request->all());
        return response()->json([
            'success' => true,
            'data' => OfferResource::collection($offers),
        ]);
    }

    public function show(int $id): JsonResponse {
        $offer = $this->offerService->getOffer($id);
        if (!$offer) {
            return response()->json(['error' => 'Offer not found'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => new OfferResource($offer),
        ]);
    }

    public function store(Request $request): JsonResponse {
        $offer = $this->offerService->createOffer($request->all());
        return response()->json([
            'success' => true,
            'data' => new OfferResource($offer),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse {
        $offer = $this->offerService->updateOffer($id, $request->all());
        return response()->json([
            'success' => true,
            'data' => new OfferResource($offer),
        ]);
    }

    public function destroy(int $id): JsonResponse {
        $this->offerService->deleteOffer($id);
        return response()->json(['success' => true, 'message' => 'Offer deleted'], 200);
    }

    public function publish(int $id): JsonResponse {
        $offer = $this->offerService->publishOffer($id);
        return response()->json([
            'success' => true,
            'data' => new OfferResource($offer),
            'message' => 'Offer published successfully',
        ]);
    }

    public function featured(): JsonResponse {
        $offers = $this->offerService->getFeaturedOffers();
        return response()->json([
            'success' => true,
            'data' => OfferResource::collection($offers),
        ]);
    }

    public function search(Request $request): JsonResponse {
        $offers = $this->offerService->searchOffers($request->all());
        return response()->json([
            'success' => true,
            'data' => OfferResource::collection($offers),
        ]);
    }
}
