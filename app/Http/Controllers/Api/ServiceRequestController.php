<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceRequestResource;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ServiceRequest::query()
            ->with(['customer', 'category'])
            ->where('status', 'open');

        if ($q = $request->query('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($cat = $request->query('category_id')) {
            $query->where('category_id', $cat);
        }

        $requests = $query->latest()->paginate(15);

        return ServiceRequestResource::collection($requests)->response();
    }

    public function show(ServiceRequest $serviceRequest): JsonResponse
    {
        $serviceRequest->load(['customer', 'category']);
        return (new ServiceRequestResource($serviceRequest))->response();
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'customer') {
            return response()->json(['message' => 'Hanya customer yang dapat membuat permintaan jasa.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|gte:budget_min',
            'work_mode' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
        ]);

        $validated['customer_id'] = $user->id;
        $validated['status'] = 'open';

        $serviceRequest = ServiceRequest::create($validated);
        $serviceRequest->load(['customer', 'category']);

        return (new ServiceRequestResource($serviceRequest))
            ->response()
            ->setStatusCode(201);
    }
}

