<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceOfferRequest;
use App\Models\Category;
use App\Models\ServiceOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ServiceOfferController extends Controller
{
    private const STATUSES = ['active', 'inactive'];

    public function index(Request $request)
    {
        $categories = Category::all();

        $offers = ServiceOffer::with(['provider:id,name,avatar', 'category'])
            ->where('status', 'active')
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

        return view('service-offers.index', compact('offers', 'categories'));
    }

    public function mine(Request $request)
    {
        $offers = $request->user()->serviceOffers()->with('category')->latest()->paginate(12);

        return view('service-offers.mine', compact('offers'));
    }

    public function create()
    {
        Gate::authorize('create', ServiceOffer::class);

        $categories = Category::all();
        return view('service-offers.create', compact('categories'));
    }

    public function store(ServiceOfferRequest $request)
    {
        Gate::authorize('create', ServiceOffer::class);

        $offer = $request->user()->serviceOffers()->create($request->validated());

        return redirect()
            ->route('service-offers.show', $offer)
            ->with('success', 'Penawaran jasa berhasil dibuat.');
    }

    public function show(ServiceOffer $serviceOffer)
    {
        $serviceOffer->load(['provider:id,name,avatar,bio', 'category']);

        return view('service-offers.show', compact('serviceOffer'));
    }

    public function edit(ServiceOffer $serviceOffer)
    {
        Gate::authorize('update', $serviceOffer);

        $categories = Category::all();

        return view('service-offers.edit', compact('serviceOffer', 'categories'));
    }

    public function update(ServiceOfferRequest $request, ServiceOffer $serviceOffer)
    {
        Gate::authorize('update', $serviceOffer);

        $serviceOffer->update($request->validated());

        return redirect()
            ->route('service-offers.show', $serviceOffer)
            ->with('success', 'Penawaran jasa berhasil diperbarui.');
    }

    public function destroy(ServiceOffer $serviceOffer)
    {
        Gate::authorize('delete', $serviceOffer);

        $serviceOffer->delete();

        return redirect()
            ->route('service-offers.mine')
            ->with('success', 'Penawaran jasa berhasil dihapus.');
    }
}
