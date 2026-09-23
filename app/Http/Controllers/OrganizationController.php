<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Organization::class);

        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin') || $user->hasRole('admin') || strtolower($user->role ?? '') === 'admin';

        $organizations = Organization::query()
            ->with('region')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('acronym', 'like', "%{$search}%");
                });
            })
            ->when(!$isAdmin, fn ($q) => $q->where('region_id', $user->region_id))
            ->when($request->filled('region'), fn ($q) => $q->where('region_id', $request->region))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $regions = Region::all();

        return view('organizations.index', compact('organizations', 'regions'));
    }

    public function create()
    {
        $this->authorize('create', Organization::class);

        $regions = Region::orderBy('id', 'asc')->get();
        $organizations = Organization::with('region')->latest()->get();

        return view('organizations.create', compact('regions', 'organizations'));
    }

    public function store(StoreOrganizationRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('certification')) {
            $validated['certification_path'] = $request->file('certification')
                ->store('certifications', 'public');
        }

        Organization::create($validated);

        return redirect()->route('organizations.index')
            ->with('success', 'Organization registered successfully.');
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        $organization->update($request->validated());

        return back()->with('success', 'Organization updated successfully.');
    }

    public function showCertification(Organization $organization)
    {
        $this->authorize('viewCertification', $organization);

        if (!$organization->certification_path || !Storage::disk('public')->exists($organization->certification_path)) {
            abort(404, 'Certification document not found.');
        }

        return Storage::disk('public')->response(
            $organization->certification_path,
            "{$organization->acronym}_certification.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    public function toggleVerify(Organization $organization)
    {
        $this->authorize('toggleVerify', $organization);

        $organization->update([
            'is_verified' => !$organization->is_verified,
        ]);

        $status = $organization->is_verified ? 'verified' : 'unverified';

        return back()->with('success', "Organization {$status} successfully!");
    }
}