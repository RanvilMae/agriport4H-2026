<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\LsaLevel;
use App\Models\Member;
use App\Models\Organization;
use App\Models\Province;
use App\Models\Region;
use App\Models\Suffix;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    /**
     * Validation rule for the profile photo upload (JPG/PNG/WEBP, max 2 MB).
     */
    private const PHOTO_RULES = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];

    /**
     * Oldest allowed age for a member, counted by birth year.
     */
    public const MAX_AGE = 30;

    /**
     * Date of birth rules: a real past date, born no earlier than
     * January 1 of (current year - 30).
     */
    private function dobRules(): array
    {
        $earliest = now()->year - self::MAX_AGE;

        return ['nullable', 'date', 'before:today', "after_or_equal:{$earliest}-01-01"];
    }

    /**
     * Friendly messages for the date of birth rules.
     */
    private function dobMessages(): array
    {
        $earliest = now()->year - self::MAX_AGE;

        return [
            'dob.after_or_equal' => 'Members must be ' . self::MAX_AGE . " years old or younger (born in {$earliest} or later).",
            'dob.before'         => 'Date of birth must be in the past.',
            'dob.date'           => 'Please enter a valid date of birth.',
        ];
    }

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

        $request->validate(
            ['photo' => self::PHOTO_RULES, 'dob' => $this->dobRules()],
            $this->dobMessages()
        );

        $validated = $request->validated();

        $validated['crops'] = $request->input('crops', []);
        $validated['services'] = $request->input('services', []);
        $validated['farm_equipment'] = $request->input('farm_equipment', []);

        if ($request->hasFile('member_file')) {
            $validated['agri_resume_path'] = $request->file('member_file')->store('member_files', 'public');
        }

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $this->storePhoto($request->file('photo'));
        }

        // Same Member ID format as self-registration: 4H-{RegionCode}-{Year}-000-000
        $validated['member_id'] = User::generateMemberId($validated['region_id'] ?? null);

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

        return view('members.edit', [
            'member'        => $member,
            'regions'       => $regions,
            // All provinces/organizations; the form filters them by the chosen region
            'provinces'     => Province::orderBy('name')->get(),
            'organizations' => Organization::orderBy('name')->get(),
            'suffixes'      => Suffix::all(),
            'lsaLevels'     => LsaLevel::all(),
        ]);
    }

    /**
     * Update specified member in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        Gate::authorize('update', $member);

        $request->validate(
            ['photo' => self::PHOTO_RULES, 'dob' => $this->dobRules()],
            $this->dobMessages()
        );

        $validated = $request->validated();

        $validated['crops'] = $request->input('crops', []);
        $validated['services'] = $request->input('services', []);
        $validated['farm_equipment'] = $request->input('farm_equipment', []);

        if ($request->hasFile('member_file')) {
            $this->deleteFile($member->agri_resume_path);
            $validated['agri_resume_path'] = $request->file('member_file')->store('member_files', 'public');
        }

        if ($request->hasFile('photo')) {
            $this->deleteFile($member->photo_path);
            $validated['photo_path'] = $this->storePhoto($request->file('photo'));
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

        $this->deleteFile($member->agri_resume_path);
        $this->deleteFile($member->photo_path);

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted!');
    }

    /**
     * Download member PDF ID Card.
     */
    public function downloadIdCard(Member $member)
    {
        $this->authorize('view', $member);

        // No ID card until the member's profile is complete
        if (! $member->canPrintId()) {
            return back()->with('error',
                "The ID for {$member->first_name} {$member->last_name} can't be printed yet. Missing: "
                . implode(', ', $member->missingIdFields()) . '.'
            );
        }

        $member->load(['region', 'province', 'organization']);

        // Images are embedded directly so DomPDF never has to fetch URLs
        $photo = $member->photo_path
            ? $this->imageDataUri(storage_path('app/public/' . $member->photo_path))
            : null;
        $logo = $this->imageDataUri(public_path('images/logo.png'));

        // QR code linking to the member's verification page
        $qr = null;
        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(240)
                ->margin(0)
                ->errorCorrection('M')
                ->generate(route('members.show', $member));
            $qr = 'data:image/svg+xml;base64,' . base64_encode($svg);
        }

        $pdf = Pdf::loadView('members.pdf-id', compact('member', 'photo', 'logo', 'qr'))
            // CR80 / ATM card size: 85.6 x 54 mm (242.65 x 153.07 pt), front + back
            ->setPaper([0, 0, 242.65, 153.07], 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'DejaVu Sans',
            ]);

        // Opens in the browser's PDF viewer, ready to print or save
        return $pdf->stream("{$member->member_id}-ID-Card.pdf");
    }

    /**
     * Turn a local image into a data URI DomPDF can embed.
     * WEBP is converted to PNG because DomPDF can't always render it.
     */
    private function imageDataUri(?string $path): ?string
    {
        if (!$path || !is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';
        $data = file_get_contents($path);

        if ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
            $image = imagecreatefromwebp($path);
            ob_start();
            imagepng($image);
            $data = ob_get_clean();
            imagedestroy($image);
            $mime = 'image/png';
        }

        return 'data:' . $mime . ';base64,' . base64_encode($data);
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

        $member = $this->currentMember();

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

        $member = $this->currentMember();

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

        $member = $this->currentMember();

        $this->authorize('update', $member);

        $regions       = Region::orderBy('id')->get();
        $provinces     = Province::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();

        return view('members.edit', compact('user', 'member', 'regions', 'provinces', 'organizations'));
    }

    /**
     * Update authenticated member's profile details.
     */
    public function updateProfile(Request $request)
    {
        $member = $this->currentMember();

        $this->authorize('update', $member);

        $validated = $request->validate([
            // Identity (email is not editable here; it's tied to the login account)
            'first_name'           => 'required|string|max:255',
            'middle_name'          => 'nullable|string|max:255',
            'last_name'            => 'required|string|max:255',
            'suffix'               => 'nullable|string|max:20',
            'sex'                  => 'nullable|in:Male,Female',
            'civil_status'         => 'nullable|in:Single,Married,Widowed,Separated',
            'dob'                  => $this->dobRules(),
            'organization_id'      => 'nullable|exists:organizations,id',

            // Location
            'region_id'            => 'required|exists:regions,id',
            'province_id'          => 'nullable|exists:provinces,id',

            // Specialization details
            'hvcdp_category'       => 'nullable|string|max:100',
            'crops'                => 'nullable|array',
            'crops.*'              => 'string',

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
            'photo'                => self::PHOTO_RULES,
        ], $this->dobMessages());

        // Uploaded files are handled separately; they are not table columns
        unset($validated['member_file'], $validated['photo']);

        // Unticked checkboxes aren't sent, so clear lists explicitly
        $validated['crops'] = $request->input('crops', []);
        $validated['farm_equipment'] = $request->input('farm_equipment', []);

        // RSBSA number only applies when registered
        if (! $request->boolean('is_rsbsa_registered')) {
            $validated['rsbsa_no'] = null;
        }

        if ($request->hasFile('member_file')) {
            $this->deleteFile($member->agri_resume_path);
            $validated['agri_resume_path'] = $request->file('member_file')->store('member_files', 'public');
        }

        if ($request->hasFile('photo')) {
            $this->deleteFile($member->photo_path);
            $validated['photo_path'] = $this->storePhoto($request->file('photo'));
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
        $member = $this->currentMember();

        $this->authorize('view', $member);

        // Without a dedicated profile template this would print the ID card,
        // so send it through the ID route, which checks the profile is complete.
        if (! view()->exists('members.pdf-profile')) {
            return $this->downloadIdCard($member);
        }

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

        $regions       = Region::orderBy('id')->get();
        $provinces     = Province::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();

        return view('members.edit', compact('user', 'member', 'regions', 'provinces', 'organizations'));
    }

    /**
     * Download uploaded attachment file for member.
     */
    public function downloadUploadedFile(Member $member)
    {
        $this->authorize('view', $member);

        if (!$member->agri_resume_path || !Storage::disk('public')->exists($member->agri_resume_path)) {
            return back()->with('error', 'Uploaded member file not found.');
        }

        return Storage::disk('public')->download($member->agri_resume_path);
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

    // =========================================================================
    // MEMBER LOOKUP & FILE HELPERS
    // =========================================================================

    /**
     * The member profile of the logged-in user.
     * Matches on user_id first, falling back to email for older records.
     */
    private function currentMember(): Member
    {
        $user = auth()->user();

        return Member::with(['region', 'province', 'organization'])
            ->where(fn ($q) => $q->where('user_id', $user->id)->orWhere('email', $user->email))
            ->orderByRaw('user_id = ? DESC', [$user->id])
            ->firstOrFail();
    }

    /**
     * Save a profile photo to storage/app/public/member-photos.
     */
    private function storePhoto(UploadedFile $photo): string
    {
        return $photo->store('member-photos', 'public');
    }

    /**
     * Delete a file from the public disk if it exists.
     */
    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}