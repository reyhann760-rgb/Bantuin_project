<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    private const STATUSES = ['open', 'in_progress', 'completed', 'cancelled'];

    // Daftar publik semua permintaan yang masih open (+ pencarian & filter)
    public function index(Request $request)
    {
        $requests = ServiceRequest::with('customer:id,name,avatar')
            ->where('status', 'open')
            ->when($request->q, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            }))
            ->when($request->work_mode, fn ($q, $m) => $q->where('work_mode', $m))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('service-requests.index', compact('requests'));
    }

    // Daftar permintaan milik customer yang login
    public function mine(Request $request)
    {
        $requests = $request->user()->serviceRequests()->latest()->paginate(12);

        return view('service-requests.mine', compact('requests'));
    }

    public function create()
    {
        return view('service-requests.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $serviceRequest = $request->user()->serviceRequests()->create($validated);

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('success', 'Permintaan jasa berhasil dibuat.');
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load('customer:id,name,avatar,bio');

        return view('service-requests.show', compact('serviceRequest'));
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        $this->authorizeOwner($serviceRequest);

        return view('service-requests.edit', compact('serviceRequest'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $this->authorizeOwner($serviceRequest);

        $validated = $this->validated($request);
        $validated['status'] = $request->validate([
            'status' => ['required', 'in:' . implode(',', self::STATUSES)],
        ])['status'];

        $serviceRequest->update($validated);

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('success', 'Permintaan jasa berhasil diperbarui.');
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        $this->authorizeOwner($serviceRequest);

        $serviceRequest->delete();

        return redirect()
            ->route('service-requests.mine')
            ->with('success', 'Permintaan jasa berhasil dihapus.');
    }

    // Hanya pemilik yang boleh mengubah/menghapus
    private function authorizeOwner(ServiceRequest $serviceRequest): void
    {
        abort_if(
        $serviceRequest->customer_id !== Auth::id(),
            403,
            'Kamu bukan pemilik permintaan ini.'
        );
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'budget_min'  => ['nullable', 'numeric', 'min:0'],
            'budget_max'  => ['nullable', 'numeric', 'min:0', 'gte:budget_min'],
            'work_mode'   => ['nullable', 'in:online,onsite,hybrid'],
            'location'    => ['nullable', 'string', 'max:255'],
            'deadline'    => ['nullable', 'date', 'after_or_equal:today'],
        ]);
    }
}