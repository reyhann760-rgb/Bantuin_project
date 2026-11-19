<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Daftar jasa untuk admin. tipe=butuh (permintaan customer) atau tipe=sedia (penawaran provider).
     */
    public function index(Request $request): View
    {
        $tipe = $request->query('tipe') === 'sedia' ? 'sedia' : 'butuh';
        $search = trim((string) $request->query('q', ''));

        if ($tipe === 'sedia') {
            $items = ServiceOffer::with(['provider', 'category'])
                ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%"))
                ->latest()
                ->paginate(15)
                ->withQueryString();
        } else {
            $items = ServiceRequest::with(['customer', 'category'])
                ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%"))
                ->latest()
                ->paginate(15)
                ->withQueryString();
        }

        return view('admin.services.index', [
            'items' => $items,
            'tipe' => $tipe,
            'search' => $search,
            'countRequests' => ServiceRequest::count(),
            'countOffers' => ServiceOffer::count(),
        ]);
    }

    /**
     * Hapus satu permintaan jasa (Butuh Jasa).
     */
    public function destroyRequest(ServiceRequest $serviceRequest): RedirectResponse
    {
        Gate::authorize('delete', $serviceRequest);

        $title = $serviceRequest->title;
        $serviceRequest->delete();

        return redirect()
            ->route('admin.services.index', ['tipe' => 'butuh'])
            ->with('success', 'Permintaan jasa "' . $title . '" berhasil dihapus.');
    }

    /**
     * Hapus satu penawaran jasa (Menyediakan Jasa).
     */
    public function destroyOffer(ServiceOffer $serviceOffer): RedirectResponse
    {
        Gate::authorize('delete', $serviceOffer);

        $title = $serviceOffer->title;
        $serviceOffer->delete();

        return redirect()
            ->route('admin.services.index', ['tipe' => 'sedia'])
            ->with('success', 'Penawaran jasa "' . $title . '" berhasil dihapus.');
    }
}
