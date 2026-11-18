<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceOfferResource;
use App\Models\ServiceOffer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceOfferController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ServiceOffer::query()
            ->with(['provider', 'category'])
            ->where('status', 'active');

        if ($q = $request->query('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($cat = $request->query('category_id')) {
            $query->where('category_id', $cat);
        }

        $offers = $query->latest()->paginate(15);

        return ServiceOfferResource::collection($offers)->response();
    }

    public function show(ServiceOffer $serviceOffer): JsonResponse
    {
        $serviceOffer->load(['provider', 'category']);
        return (new ServiceOfferResource($serviceOffer))->response();
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'provider') {
            return response()->json(['message' => 'Hanya penyedia jasa yang dapat membuat penawaran jasa.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'nullable|numeric|min:0',
            'price_type' => 'nullable|string|max:50',
            'work_mode' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'portfolio_url' => 'nullable|url|max:255',
        ]);

        $validated['provider_id'] = $user->id;
        $validated['status'] = 'active';

        $serviceOffer = ServiceOffer::create($validated);
        $serviceOffer->load(['provider', 'category']);

        return (new ServiceOfferResource($serviceOffer))
            ->response()
            ->setStatusCode(201);
    }
}

