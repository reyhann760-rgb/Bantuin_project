<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard admin: ringkasan statistik platform.
     */
    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'customers' => User::where('role', 'customer')->count(),
            'providers' => User::where('role', 'provider')->count(),
            'categories' => Category::count(),
            'requests' => ServiceRequest::count(),
            'openRequests' => ServiceRequest::where('status', 'open')->count(),
            'offers' => ServiceOffer::count(),
            'activeOffers' => ServiceOffer::where('status', 'active')->count(),
            'conversations' => Conversation::count(),
        ];

        $latestUsers = User::where('role', '!=', 'admin')->latest()->take(5)->get();
        $latestRequests = ServiceRequest::with(['customer', 'category'])->latest()->take(5)->get();
        $latestOffers = ServiceOffer::with(['provider', 'category'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'latestUsers', 'latestRequests', 'latestOffers'));
    }
}
