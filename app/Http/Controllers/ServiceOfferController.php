<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ServiceOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $categories = Category::all();
        return view('service-offers.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $offer = $request->user()->serviceOffers()->create($this->validated($request));

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
        $this->authorizeOwner($serviceOffer);
        $categories = Category::all();

        return view('service-offers.edit', compact('serviceOffer', 'categories'));
    }

    public function update(Request $request, ServiceOffer $serviceOffer)
    {
        $this->authorizeOwner($serviceOffer);

        $validated = $this->validated($request);
        $validated['status'] = $request->validate([
            'status' => ['required', 'in:' . implode(',', self::STATUSES)],
        ])['status'];

        $serviceOffer->update($validated);

        return redirect()
            ->route('service-offers.show', $serviceOffer)
            ->with('success', 'Penawaran jasa berhasil diperbarui.');
    }

    public function destroy(ServiceOffer $serviceOffer)
    {
        $this->authorizeOwner($serviceOffer);

        $serviceOffer->delete();

        return redirect()
            ->route('service-offers.mine')
            ->with('success', 'Penawaran jasa berhasil dihapus.');
    }

    private function authorizeOwner(ServiceOffer $serviceOffer): void
    {
        abort_if(
            $serviceOffer->provider_id !== Auth::id(),
            403,
            'Kamu bukan pemilik penawaran ini.'
        );
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string', 'min:20'],
            'category_id'   => ['nullable', 'exists:categories,id'],
            'price'         => ['nullable', 'numeric', 'min:0'],
            'price_type'    => ['nullable', 'in:fixed,hourly,negotiable'],
            'work_mode'     => ['nullable', 'in:online,onsite,hybrid'],
            'location'      => ['nullable', 'string', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
        ]);
    }
}
