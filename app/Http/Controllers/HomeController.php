<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan beranda utama dengan data nyata dari database:
     * - Kategori jasa
     * - 3 permintaan (Butuh Jasa) terbaru dengan status 'open'
     * - 3 penawaran (Menyediakan Jasa) terbaru dengan status 'active'
     */
    public function index(): View
    {
        $categories = Category::latest()->get();

        $recentRequests = ServiceRequest::with(['customer', 'category'])
            ->where('status', 'open')
            ->latest()
            ->take(3)
            ->get();

        $recentOffers = ServiceOffer::with(['provider', 'category'])
            ->where('status', 'active')
            ->latest()
            ->take(3)
            ->get();

        return view('home', [
            'categories' => $categories,
            'recentRequests' => $recentRequests,
            'recentOffers' => $recentOffers,
        ]);
    }
}

