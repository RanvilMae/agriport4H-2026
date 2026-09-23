<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\LsaLevel;
use App\Models\Member;
use App\Models\Province;
use App\Models\Region;
use App\Models\Suffix;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    /**
     * Display a listing of members.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'Member') {
            return redirect()->route('member.profile.show');
        }

        $this->authorize('viewAny', Member::class);

        $search = $request->input('search');
        $isRegionalUser = in_array($user->role, ['President', 'Coordinator']);

        $regions = Region::when($isRegionalUser, fn ($q) => $q->where('id', $user->region_id))->get();

        $members = Member::with(['region', 'organization'])
            ->when($isRegionalUser, fn ($q) => $q->where('region_id', $user->region_id))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate(15);

        return view('members.index', compact('members', 'regions'));
    }

    /**
     * Show form to create a new member.
     */
    public function create()
    {
        $this->authorize('create', Member::class);

        $user = auth()->user();
        $isRegionalUser = in_array($user->role, ['President', 'Coordinator']);

        $regions = Region::with(['provinces', 'organizations'])
            ->when($isRegionalUser, fn ($q) => $q->where('id', $user->region_id))
            ->orderBy('id')
            ->get();

        return view('members.create', [
            'regions'      => $regions,
            'lsaLevels'    => LsaLevel::all(),
            'suffixes'     => Suffix::all(),
            'userRegionId' => $user->region_id ?? '',
        ]);
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $this->authorize('create', Member::class);

        $validated = $request->validated();

        $validated['crops'] = $request->input('crops', []);
        $validated['services'] = $request->input('services', []);
        $validated['farm_equipment'] = $request->input('farm_equipment', []);

        if ($request->hasFile('member_file')) {
            $validated['member_file_path'] = $request->file('member_file')->store('member_files', 'public');
        }

        // Unique Member UID generation
        $currentYear = now()->year;
        $regionId = $validated['region_id'];
        $count = Member::where('region_id', $regionId)->whereYear('created_at', $currentYear)->count() + 1;

        $validated['member_id'] = sprintf(
            '4H-PH-%s-%s-%s-%04d',
            str_pad($regionId, 2, '0', STR_PAD_LEFT),
            $currentYear,
            str_pad($regionId, 3, '0', STR_PAD_LEFT),
            $count
        );

        Member::create($validated);

        return redirect()->route('members.index')
            ->with('success', "Member registered successfully! ID: {$validated['member_id']}");
    }

    /**
     * Display member ID preview / QR card.
     */
    public function show(Member $member)
    {
        $this->authorize('view', $member);

        $member->load(['region', 'province', 'organization']);
        $qrData = route('members.show', $member->id);

        return view('members.id', compact('member', 'qrData'));
    }

    /**
     * Show form to edit member record.
     */
    public function edit(Member $member)
    {
        $this->authorize('update', $member);

        $user = auth()->user();
        $isRegionalUser = in_array($user->role, ['President', 'Coordinator']);

        $regions = Region::with('provinces')
            ->when($isRegionalUser, fn ($q) => $q->where('id', $user->region_id))
            ->get();

        $provinces = Province::where('region_id', $member->region_id)->get();

        return view('members.edit', [
            'member'    => $member,
            'regions'   => $regions,
            'provinces' => $provinces,
            'suffixes'  => Suffix::all(),
            'lsaLevels' => LsaLevel::all(),
        ]);
    }

    /**
     * Update specified member in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        Gate::authorize('update', $member);

        $validated = $request->validated();

        $validated['crops'] = $request->input('crops', []);
        $validated['services'] = $request->input('services', []);
        $validated['farm_equipment'] = $request->input('farm_equipment', []);

        if ($request->hasFile('member_file')) {
            if ($member->member_file_path && Storage::disk('public')->exists($member->member_file_path)) {
                Storage::disk('public')->delete($member->member_file_path);
            }
            $validated['member_file_path'] = $request->file('member_file')->store('member_files', 'public');
        }

        $member->update($validated);

        return redirect()->route('members.index')
            ->with('success', 'Member record updated successfully!');
    }

    /**
     * Remove member from storage.
     */
    public function destroy(Member $member)
    {
        $this->authorize('delete', $member);

        if ($member->member_file_path && Storage::disk('public')->exists($member->member_file_path)) {
            Storage::disk('public')->delete($member->member_file_path);
        }

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted!');
    }

    /**
     * Download member PDF ID Card.
     */
    public function downloadIdCard(Member $member)
    {
        $this->authorize('view', $member);

        $member->load(['region', 'province']);

        $pdf = Pdf::loadView('members.pdf-id', compact('member'))
            ->setPaper([0, 0, 250, 400], 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        return $pdf->download("{$member->last_name}-LSA-ID.pdf");
    }

    /**
     * Verify member account status.
     */
    public function verify(Member $member)
    {
        $this->authorize('verify', $member);

        $member->update([
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return redirect()->route('members.show', $member)
            ->with('success', "Account for {$member->first_name} has been officially verified.");
    }

    // =========================================================================
    // MEMBER SELF-SERVICE METHODS
    // =========================================================================

    /**
     * Display authenticated member's own profile overview.
     * Route: member.profile.show
     */
    public function showProfile()
    {
        $user = auth()->user();

        $member = Member::with(['region', 'province', 'organization'])
            ->where('email', $user->email)
            ->firstOrFail();

        $this->authorize('view', $member);

        return view('members.show', compact('user', 'member'));
    }

    /**
     * Display authenticated member's Agri-Resume preview.
     * Route: member.agri-resume.preview
     */
    public function showAgriResumePreview()
    {
        $user = auth()->user();

        $member = Member::with(['region', 'province', 'organization'])
            ->where('email', $user->email)
            ->firstOrFail();

        $this->authorize('view', $member);

        return view('members.agri-resume-preview', compact('user', 'member'));
    }

    /**
     * Display Agri-Resume preview for a specific member (Admin/Coordinator/President access).
     * Route: members.agri-resume
     */
    public function showMemberAgriResume(Member $member)
    {
        $this->authorize('view', $member);

        $user = auth()->user();
        $member->load(['region', 'province', 'organization']);

        return view('members.agri-resume-preview', compact('user', 'member'));
    }

    /**
     * Show form for current user to edit their profile details.
     */
    public function editProfile()
    {
        $user = auth()->user();

        $member = Member::with(['region', 'province', 'organization'])
            ->where('email', $user->email)
            ->firstOrFail();

        $this->authorize('update', $member);

        $regions = Region::all();
        $provinces = Province::where('region_id', $member->region_id)->get();

        return view('members.edit', compact('user', 'member', 'regions', 'provinces'));
    }

    /**
     * Update authenticated member's profile details.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $member = Member::where('email', $user->email)->firstOrFail();

        $this->authorize('update', $member);

        $validated = $request->validate([
            'contact_no'           => 'required|string|max:20',
            'occupation'           => 'nullable|string|max:255',
            'specialization'       => 'required|string',
            'city_municipality'    => 'required|string',
            'barangay'             => 'required|string',
            'zip_code'             => 'nullable|string|max:4',
            'highest_education'    => 'nullable|string|max:100',
            'degree_course'        => 'nullable|string|max:150',
            'school_name'          => 'nullable|string|max:200',
            'land_ownership'       => 'nullable|string',
            'farm_area'            => 'nullable|string|max:50',
            'is_rsbsa_registered'  => 'nullable|boolean',
            'rsbsa_no'             => 'nullable|string|max:50',
            'farm_equipment'       => 'nullable|array',
            'farm_equipment.*'     => 'string',
            'agri_skills'          => 'nullable|string|max:1000',
            'certifications'       => 'nullable|string|max:255',
            'member_file'          => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('member_file')) {
            if ($member->member_file_path && Storage::disk('public')->exists($member->member_file_path)) {
                Storage::disk('public')->delete($member->member_file_path);
            }
            $validated['member_file_path'] = $request->file('member_file')->store('member_files', 'public');
        }

        $member->update($validated);

        return redirect()->route('member.profile.show')
            ->with('success', 'Your member details have been updated successfully.');
    }

    /**
     * Export Member Data PDF report.
     */
    public function downloadProfilePdf(Request $request)
    {
        $user = auth()->user();

        $member = Member::with(['region', 'province', 'organization'])
            ->where('email', $user->email)
            ->first();

        if (!$member) {
            return back()->with('error', 'No Member record found matching email: ' . $user->email);
        }

        $this->authorize('view', $member);

        return $this->generatePdfResponse($member);
    }

    /**
     * Admin view to inspect individual member profile.
     */
    public function showMemberData(Member $member)
    {
        $this->authorize('view', $member);

        $user = auth()->user();
        $member->load(['region', 'province', 'organization']);

        $regions = Region::all();
        $provinces = Province::where('region_id', $member->region_id)->get();

        return view('members.edit', compact('user', 'member', 'regions', 'provinces'));
    }

    /**
     * Download uploaded attachment file for member.
     */
    public function downloadUploadedFile(Member $member)
    {
        $this->authorize('view', $member);

        if (!$member->member_file_path || !Storage::disk('public')->exists($member->member_file_path)) {
            return back()->with('error', 'Uploaded member file not found.');
        }

        return Storage::disk('public')->download($member->member_file_path);
    }

    /**
     * Helper to generate Member Profile PDF download via DomPDF.
     */
    protected function generatePdfResponse(Member $member)
    {
        $pdfView = view()->exists('members.pdf-profile') ? 'members.pdf-profile' : 'members.pdf-id';

        if (!view()->exists($pdfView)) {
            abort(404, 'PDF view template not found.');
        }

        $pdf = Pdf::loadView($pdfView, compact('member'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        return $pdf->download("Member-Data-{$member->last_name}.pdf");
    }
}