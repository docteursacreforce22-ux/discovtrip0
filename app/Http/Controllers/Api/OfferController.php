<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Services\OfferService;
use App\Http\Resources\OfferResource;
use Illuminate\Http\{JsonResponse, Request};

class OfferController
{
    public function __construct(private OfferService $offerService) {}

    public function index(Request $request): JsonResponse
    {
        // CORRECTION : valider les paramètres de recherche
        $validated = $request->validate([
            'search'    => ['nullable', 'string', 'max:120'],
            'category'  => ['nullable', 'string', 'in:cultural,gastronomy,nature,adventure,wellness,urban'],
            'city_id'   => ['nullable', 'integer', 'exists:cities,id'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'sort'      => ['nullable', 'string', 'in:newest,price_asc,price_desc,rating'],
            'per_page'  => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $offers = $this->offerService->searchOffers($validated);

        return response()->json([
            'success' => true,
            'data'    => OfferResource::collection($offers),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $offer = $this->offerService->getOffer($id);

        if (! $offer) {
            return response()->json(['error' => 'Offer not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new OfferResource($offer),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // CORRECTION : $request->all() remplacé par validation explicite
        // Seuls les champs listés ici peuvent être créés — pas de mass assignment
        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'category'          => ['required', 'string', 'in:cultural,gastronomy,nature,adventure,wellness,urban'],
            'city_id'           => ['required', 'integer', 'exists:cities,id'],
            'base_price'        => ['required', 'numeric', 'min:0'],
            'currency'          => ['nullable', 'string', 'size:3'],
            'duration_minutes'  => ['nullable', 'integer', 'min:15'],
            'min_participants'  => ['nullable', 'integer', 'min:1'],
            'max_participants'  => ['nullable', 'integer', 'min:1'],
            'languages'         => ['nullable', 'array'],
            'languages.*'       => ['string', 'max:10'],
            'meeting_point'     => ['nullable', 'string', 'max:500'],
            'included_items'    => ['nullable', 'array'],
            'excluded_items'    => ['nullable', 'array'],
            'guide_type'        => ['nullable', 'string', 'in:assigned,agency,on_site'],
            'payment_mode'      => ['nullable', 'string', 'in:online,on_site,both'],
        ]);

        $offer = $this->offerService->createOffer($validated);

        return response()->json([
            'success' => true,
            'data'    => new OfferResource($offer),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        // CORRECTION : validation stricte, pas de $request->all()
        $validated = $request->validate([
            'title'             => ['sometimes', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'category'          => ['sometimes', 'string', 'in:cultural,gastronomy,nature,adventure,wellness,urban'],
            'city_id'           => ['sometimes', 'integer', 'exists:cities,id'],
            'base_price'        => ['sometimes', 'numeric', 'min:0'],
            'currency'          => ['nullable', 'string', 'size:3'],
            'duration_minutes'  => ['nullable', 'integer', 'min:15'],
            'min_participants'  => ['nullable', 'integer', 'min:1'],
            'max_participants'  => ['nullable', 'integer', 'min:1'],
            'languages'         => ['nullable', 'array'],
            'languages.*'       => ['string', 'max:10'],
            'meeting_point'     => ['nullable', 'string', 'max:500'],
            'included_items'    => ['nullable', 'array'],
            'excluded_items'    => ['nullable', 'array'],
            'guide_type'        => ['nullable', 'string', 'in:assigned,agency,on_site'],
            'payment_mode'      => ['nullable', 'string', 'in:online,on_site,both'],
            // Champs INTERDITS : status, is_featured, views_count, published_at
            // → passent par les actions dédiées (publish, feature)
        ]);

        $offer = $this->offerService->updateOffer($id, $validated);

        return response()->json([
            'success' => true,
            'data'    => new OfferResource($offer),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->offerService->deleteOffer($id);

        return response()->json(['success' => true, 'message' => 'Offer deleted'], 200);
    }

    public function publish(int $id): JsonResponse
    {
        $offer = $this->offerService->publishOffer($id);

        return response()->json([
            'success' => true,
            'data'    => new OfferResource($offer),
            'message' => 'Offer published successfully',
        ]);
    }

    public function featured(): JsonResponse
    {
        $offers = $this->offerService->getFeaturedOffers();

        return response()->json([
            'success' => true,
            'data'    => OfferResource::collection($offers),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search'    => ['nullable', 'string', 'max:120'],
            'category'  => ['nullable', 'string', 'in:cultural,gastronomy,nature,adventure,wellness,urban'],
            'city_id'   => ['nullable', 'integer', 'exists:cities,id'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'sort'      => ['nullable', 'string', 'in:newest,price_asc,price_desc,rating'],
        ]);

        $offers = $this->offerService->searchOffers($validated);

        return response()->json([
            'success' => true,
            'data'    => OfferResource::collection($offers),
        ]);
    }
}
