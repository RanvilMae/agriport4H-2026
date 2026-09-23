<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the read-only profile view.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        
        // Eager load relations attached to member if they exist
        $member = $user->member?->load(['region', 'province', 'organization']);

        return view('profile.show', compact('user', 'member'));
    }

    /**
     * Display the profile edit form (update.blade.php).
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $member = $user->member;

        // Fetch location options for the dropdown lists
        $regions = Region::orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();

        return view('profile.edit', compact('user', 'member', 'regions', 'provinces'));
    }

    /**
     * Update the member profile information.
     */
    public function update(Request $request): RedirectResponse
{
    $user = $request->user();

    $validated = $request->validate([
        // Section I: Identity & Demographics
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'suffix' => 'nullable|string|max:50',
        'dob' => 'required|date',
        'sex' => 'required|in:male,female',
        'civil_status' => 'required|string|in:single,married,widowed,separated',
        'contact_no' => 'required|string|max:20',
        'email' => 'required|email|max:255|unique:users,email,' . $user->id,

        // Section II: Address & Location Details
        'region_id' => 'nullable|exists:regions,id',
        'province_id' => 'nullable|exists:provinces,id',
        'city_municipality' => 'nullable|string|max:255',
        'district' => 'nullable|string|max:255',
        'barangay' => 'nullable|string|max:255',
        'zip_code' => 'nullable|string|max:20',

        // Section III: Professional & Program Details
        'member_type' => 'nullable|string|max:255',
        'occupation' => 'nullable|string|max:255',
        'organization_id' => 'nullable|exists:organizations,id',
        'specialization' => 'nullable|string|max:255',
        'hvcdp_category' => 'nullable|string|max:255',
        'internship' => 'nullable|string|max:255',
        'scholarship' => 'nullable|string|max:255',
        'lsa_level' => 'nullable|string|max:255',
        'lsa_type' => 'nullable|string|max:255',
        'training_course' => 'nullable|string|max:255',
        'crops_input' => 'nullable|string',
        'services_input' => 'nullable|string',

        // Section IV: Agri-Resume & Field Experience Details
        'highest_education' => 'nullable|string|max:255',
        'degree_course' => 'nullable|string|max:255',
        'school_name' => 'nullable|string|max:255',
        'land_ownership' => 'nullable|string|max:255',
        'farm_area' => 'nullable|numeric|min:0',
        'is_rsbsa_registered' => 'nullable|boolean',
        'rsbsa_no' => 'nullable|required_if:is_rsbsa_registered,1|string|max:255',
        'farm_equipment' => 'nullable|string',
        'agri_skills' => 'nullable|string',
        'certifications' => 'nullable|string',
        'bio_summary' => 'nullable|string|max:1000',
    ]);

    // 1. Sync User credentials
    if ($user->email !== $validated['email']) {
        $user->email_verified_at = null;
    }
    $user->email = $validated['email'];
    $user->name = trim("{$validated['first_name']} {$validated['last_name']}");
    $user->save();

    // 2. Map array-based or transformed inputs
    $validated['crops'] = isset($validated['crops_input']) 
        ? array_filter(array_map('trim', explode(',', $validated['crops_input']))) 
        : null;

    $validated['services'] = isset($validated['services_input']) 
        ? array_filter(array_map('trim', explode(',', $validated['services_input']))) 
        : null;

    $validated['is_rsbsa_registered'] = $request->has('is_rsbsa_registered');

    // 3. Save or Update Member record
    $user->member()->updateOrCreate(
        ['user_id' => $user->id],
        $validated
    );

    return Redirect::route('profile.show')->with('status', 'profile-updated');
}
    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}