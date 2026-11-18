<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequestRequest;
use App\Models\Category;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ServiceRequestController extends Controller
{
    private const STATUSES = ['open', 'in_progress', 'completed', 'cancelled'];

    // Daftar publik semua permintaan yang masih open (+ pencarian & filter)
    public function index(Request $request)
    {
        $categories = Category::all();

        $requests = ServiceRequest::with(['customer:id,name,avatar', 'category'])
            ->where('status', 'open')
            ->when($request->q, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            }))
            ->when($request->work_mode, fn ($q, $m) => $q->where('work_mode', $m))
            ->when($request->category, function ($q, $cat) {
                $q->where(function ($w) use ($cat) {
                    $w->where('category_id', $cat)
                      ->orWhereHas('category', fn ($c) => $c->where('slug', $cat)->orWhere('name', $cat));
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('service-requests.index', compact('requests', 'categories'));
    }

    // Daftar permintaan milik customer yang login
    public function mine(Request $request)
    {
        $requests = $request->user()->serviceRequests()->with('category')->latest()->paginate(12);

        return view('service-requests.mine', compact('requests'));
    }

    public function create()
    {
        Gate::authorize('create', ServiceRequest::class);

        $categories = Category::all();
        return view('service-requests.create', compact('categories'));
    }

    public function store(ServiceRequestRequest $request)
    {
        Gate::authorize('create', ServiceRequest::class);

        $serviceRequest = $request->user()->serviceRequests()->create($request->validated());

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('success', 'Permintaan jasa berhasil dibuat.');
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['customer:id,name,avatar,bio', 'category']);

        return view('service-requests.show', compact('serviceRequest'));
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        Gate::authorize('update', $serviceRequest);

        $categories = Category::all();

        return view('service-requests.edit', compact('serviceRequest', 'categories'));
    }

    public function update(ServiceRequestRequest $request, ServiceRequest $serviceRequest)
    {
        Gate::authorize('update', $serviceRequest);

        $serviceRequest->update($request->validated());

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('success', 'Permintaan jasa berhasil diperbarui.');
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        Gate::authorize('delete', $serviceRequest);

        $serviceRequest->delete();

        return redirect()
            ->route('service-requests.mine')
            ->with('success', 'Permintaan jasa berhasil dihapus.');
    }
}
