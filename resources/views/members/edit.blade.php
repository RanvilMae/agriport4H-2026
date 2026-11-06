<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Member Record: {{ $member->first_name }} {{ $member->last_name }}
        </h2>
    </x-slot>

    @php
        // This view is shared by the admin edit page and the member's own profile edit page.
        $isSelfEdit = request()->routeIs('member.profile.*');
        $formAction = $isSelfEdit ? route('member.profile.update') : route('members.update', $member->id);
        $backUrl    = $isSelfEdit ? route('member.profile.show') : route('members.index');
        $photoUrl   = $member->photo_path ? asset('storage/' . $member->photo_path) : null;
        $dobValue   = $member->dob ? \Illuminate\Support\Carbon::parse($member->dob)->format('Y-m-d') : '';
        $missingId  = $member->missingIdFields();
    @endphp

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- Navigation Header --}}
        <div class="flex items-center justify-between">
            <a href="{{ $backUrl }}" class="text-sm text-gray-500 hover:text-emerald-600 font-bold flex items-center transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> {{ $isSelfEdit ? 'Back to My Profile' : 'Back to Directory' }}
            </a>
            <span class="text-xs font-semibold px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">
                UID: {{ $member->member_id ?? 'Unassigned' }}
            </span>
        </div>

        {{-- What is still needed before the ID can be printed --}}
        @if(! empty($missingId))
            <div class="flex items-start gap-3 p-4 border rounded-2xl bg-amber-50 border-amber-200">
                <i class="mt-0.5 fas fa-id-card text-amber-600"></i>
                <div class="text-xs">
                    <p class="font-black text-amber-800">Fill these in so the ID card can be printed:</p>
                    <p class="mt-1 font-semibold text-amber-700">{{ implode(' · ', $missingId) }}</p>
                </div>
            </div>
        @else
            <div class="flex items-center gap-3 p-4 text-xs font-bold border rounded-2xl bg-emerald-50 border-emerald-100 text-emerald-700">
                <i class="fas fa-circle-check text-emerald-600"></i>
                All required details are complete. The ID card can be printed.
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    <h3 class="text-sm font-bold text-red-800">Please fix the following validation errors:</h3>
                </div>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Main Form Container --}}
        <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Profile Photo --}}
            <div x-data="{ preview: @js($photoUrl), fileName: '' }"
                 class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-8 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xs font-black text-emerald-600 uppercase tracking-[0.2em] flex items-center gap-2">
                        <i class="fas fa-camera"></i> Profile Photo
                    </h3>
                    <span class="text-[10px] text-gray-400 font-medium">Used on the Digital ID and Member Registry</span>
                </div>

                <div class="p-8 flex flex-col sm:flex-row sm:items-center gap-6">
                    {{-- Preview --}}
                    <div class="w-32 h-32 rounded-[1.75rem] overflow-hidden bg-emerald-50 border-2 border-dashed border-emerald-200 flex items-center justify-center shrink-0">
                        <template x-if="preview">
                            <img :src="preview" class="object-cover w-full h-full" alt="Profile photo preview">
                        </template>
                        <template x-if="!preview">
                            <div class="flex flex-col items-center text-emerald-300">
                                <i class="text-4xl fas fa-user"></i>
                                <span class="mt-1 text-[9px] font-black uppercase tracking-widest">No Photo</span>
                            </div>
                        </template>
                    </div>

                    {{-- Picker --}}
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-bold text-gray-800">Upload a clear, front-facing photo</p>
                            <p class="text-xs text-gray-500">JPG, PNG or WEBP · max 2 MB · a square photo looks best</p>
                        </div>

                        <label for="photo"
                               class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-black tracking-wider text-white uppercase transition bg-emerald-600 cursor-pointer rounded-2xl hover:bg-emerald-700 shadow-lg shadow-emerald-600/20">
                            <i class="fas fa-upload"></i>
                            <span x-text="preview ? 'Change Photo' : 'Choose Photo'"></span>
                        </label>
                        <input id="photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="sr-only"
                               @change="const f = $event.target.files[0]; if (f) { preview = URL.createObjectURL(f); fileName = f.name; }">

                        <p x-show="fileName" x-cloak class="text-[11px] font-bold text-emerald-700">
                            <i class="mr-1 fas fa-check-circle"></i>
                            <span x-text="fileName"></span> — will be saved when you click "Save Member Details"
                        </p>

                        @error('photo')
                            <p class="text-xs font-bold text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- 1. Identity & Demographics Section --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-8 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xs font-black text-emerald-600 uppercase tracking-[0.2em] flex items-center gap-2">
                        <i class="fas fa-user"></i> I. Identity & Demographics
                    </h3>
                    <span class="text-[10px] text-gray-400 font-medium">Step 1 of 3</span>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        {{-- First Name --}}
                        <div>
                            <label for="first_name" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">First Name</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $member->first_name) }}" required
                                class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                        </div>

                        {{-- Middle Name --}}
                        <div>
                            <label for="middle_name" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $member->middle_name) }}"
                                class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                        </div>

                        {{-- Last Name --}}
                        <div>
                            <label for="last_name" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Last Name</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $member->last_name) }}" required
                                class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                        </div>

                        {{-- Suffix --}}
                        <div>
                            <label for="suffix" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Suffix</label>
                            <input type="text" name="suffix" id="suffix" value="{{ old('suffix', $member->suffix) }}" placeholder="e.g. Jr., III"
                                class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                        </div>

                        {{-- Sex --}}
                        <div>
                            <label for="sex" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Sex</label>
                            <select name="sex" id="sex" class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                                <option value="">Select Sex</option>
                                <option value="Male" {{ old('sex', $member->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex', $member->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        {{-- Civil Status --}}
                        <div>
                            <label for="civil_status" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Civil Status</label>
                            <select name="civil_status" id="civil_status" class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                                <option value="">Select Status</option>
                                <option value="Single" {{ old('civil_status', $member->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('civil_status', $member->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Widowed" {{ old('civil_status', $member->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="Separated" {{ old('civil_status', $member->civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                        </div>

                        {{-- Date of Birth --}}
                        <div>
                            <label for="dob" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Date of Birth</label>
                            <input type="date" name="dob" id="dob" value="{{ old('dob', $dobValue) }}"
                                min="{{ now()->year - \App\Http\Controllers\MemberController::MAX_AGE }}-01-01"
                                max="{{ now()->subDay()->format('Y-m-d') }}"
                                class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                            <p class="mt-1 text-[10px] font-semibold text-gray-400">Members must be {{ \App\Http\Controllers\MemberController::MAX_AGE }} or younger (born {{ now()->year - \App\Http\Controllers\MemberController::MAX_AGE }} or later).</p>
                            @error('dob')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contact Number --}}
                        <div>
                            <label for="contact_no" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Contact Number</label>
                            <input type="text" name="contact_no" id="contact_no" required value="{{ old('contact_no', $member->contact_no) }}"
                                class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                        </div>

                        {{-- Email Address --}}
                        <div>
                            <label for="email" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Email Address</label>
                            @if($isSelfEdit)
                                {{-- Tied to the login account, so it can't be changed here --}}
                                <input type="email" id="email" value="{{ $member->email }}" readonly
                                    title="Your email is linked to your login and can't be changed here"
                                    class="w-full text-sm font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-2xl px-3 py-2 cursor-not-allowed outline-none">
                            @else
                                <input type="email" name="email" id="email" value="{{ old('email', $member->email) }}"
                                    class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                            @endif
                        </div>

                        {{-- Occupation --}}
                        <div>
                            <label for="occupation" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Occupation</label>
                            <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $member->occupation) }}"
                                class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                        </div>

                        {{-- Organization / Club (Filtered by Member Region) --}}
                        <div>
                            <label for="organization_id" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Organization / Club</label>
                            <select name="organization_id" id="organization_id" class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                                <option value="">Select Organization</option>
                                @foreach($organizations ?? [] as $org)
                                    <option value="{{ $org->id }}" 
                                            data-region-id="{{ $org->region_id }}"
                                            {{ old('organization_id', $member->organization_id) == $org->id ? 'selected' : '' }}>
                                        {{ $org->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Location & Jurisdiction Section --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-8 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xs font-black text-emerald-600 uppercase tracking-[0.2em] flex items-center gap-2">
                        <i class="fas fa-map-marker-alt"></i> II. Location & Jurisdiction
                    </h3>
                    <span class="text-[10px] text-gray-400 font-medium">Step 2 of 3</span>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                        {{-- Region --}}
                        <div>
                            <label for="region_id" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Region</label>
                            <select name="region_id" id="region_id" required class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                                <option value="">Select Region</option>
                                @foreach($regions ?? [] as $region)
                                    <option value="{{ $region->id }}" {{ old('region_id', $member->region_id) == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Province --}}
                        <div>
                            <label for="province_id" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Province</label>
                            <select name="province_id" id="province_id" class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                                <option value="">Select Province</option>
                                @foreach($provinces ?? [] as $province)
                                    <option value="{{ $province->id }}"
                                            data-region-id="{{ $province->region_id }}"
                                            {{ old('province_id', $member->province_id) == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- City / Municipality --}}
                        <div>
                            <label for="city_municipality" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">City / Municipality</label>
                            <input type="text" name="city_municipality" id="city_municipality" required 
                                value="{{ old('city_municipality', $member->city_municipality ?? ($member->city_municipality_id ?? '')) }}" 
                                placeholder="Enter City / Municipality"
                                class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                        </div>

                        {{-- Barangay & Zip Inputs --}}
                        <div>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="col-span-2">
                                    <label for="barangay" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Barangay</label>
                                    <input type="text" name="barangay" id="barangay" required 
                                        value="{{ old('barangay', $member->barangay ?? ($member->barangay_id ?? '')) }}" 
                                        placeholder="Enter Barangay"
                                        class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                                </div>
                                <div>
                                    <label for="zip_code" class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Zip</label>
                                    <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code', $member->zip_code) }}" placeholder="1000"
                                        class="w-full text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-2 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Agri-Resume & Field Experience Section --}}
           <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden print:hidden">
                <!-- Card Header -->
                <div class="flex items-center justify-between px-8 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xs font-black text-emerald-600 uppercase tracking-[0.2em]">III. Agri-Resume & Field Experience Details</h3>
                    <span class="text-[10px] text-gray-400 font-medium" aria-label="Step 3 of 3">Step 3 of 3</span>
                </div>

                <div class="p-8 space-y-8">
                    {{-- Section A: Educational Background --}}
                    <section aria-labelledby="section-education">
                        <h4 id="section-education" class="text-[11px] font-black text-gray-700 uppercase tracking-wider mb-4">A. Educational Background</h4>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            {{-- Highest Education Level --}}
                            <div>
                                <label for="highest_education" class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Highest Attainment</label>
                                <select id="highest_education" name="highest_education" class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 text-sm focus:border-emerald-500">
                                    <option value="">Select Level</option>
                                    @php
                                        $educationLevels = [
                                            'High School' => 'High School Student / Graduate',
                                            'Vocational' => 'Vocational / Technical',
                                            'College Undergraduate' => 'College Undergraduate',
                                            'College Graduate' => 'College Graduate',
                                            'Post-Graduate' => 'Post-Graduate'
                                        ];
                                    @endphp
                                    @foreach($educationLevels as $value => $label)
                                        <option value="{{ $value }}" {{ old('highest_education', $member->highest_education ?? '') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('highest_education')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Degree / Course --}}
                            <div>
                                <label for="degree_course" class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Degree / Course (if applicable)</label>
                                <input type="text" id="degree_course" name="degree_course" value="{{ old('degree_course', $member->degree_course ?? '') }}" placeholder="e.g. BS Agriculture, BS Agribusiness" class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                @error('degree_course')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- School / Institution --}}
                            <div>
                                <label for="school_name" class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">School / Institution</label>
                                <input type="text" id="school_name" name="school_name" value="{{ old('school_name', $member->school_name ?? '') }}" placeholder="Name of university or school" class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                @error('school_name')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <hr class="border-gray-100">

                    {{-- Section B: Farm Profile & Operational Scale --}}
                    <section aria-labelledby="section-farm-profile" x-data="{ 
                        isRsbsa: @json(old('is_rsbsa_registered', (string)($member->is_rsbsa_registered ?? '0')) === '1')
                    }">
                        <h4 id="section-farm-profile" class="text-[11px] font-black text-gray-700 uppercase tracking-wider mb-4">B. Farm Profile & Scale of Operation</h4>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            {{-- Land Ownership --}}
                            <div>
                                <label for="land_ownership" class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Land Tenure / Ownership</label>
                                <select id="land_ownership" name="land_ownership" class="w-full text-sm border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">Select Ownership</option>
                                    @php
                                        $landOwnerships = [
                                            'Owner' => 'Owner / Titled',
                                            'Tenant' => 'Tenant',
                                            'Lessee' => 'Lessee / Renter',
                                            'Co-Owner' => 'Family Co-Owner'
                                        ];
                                    @endphp
                                    @foreach($landOwnerships as $value => $label)
                                        <option value="{{ $value }}" {{ old('land_ownership', $member->land_ownership ?? '') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('land_ownership')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Farm Area --}}
                            <div>
                                <label for="farm_area" class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Total Farm Area (Hectares / SqM)</label>
                                <input type="text" id="farm_area" name="farm_area" value="{{ old('farm_area', $member->farm_area ?? '') }}" placeholder="e.g. 1.5 Ha or 500 SqM" class="w-full text-sm border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500">
                                @error('farm_area')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- RSBSA Registered Toggle --}}
                            <div>
                                <label for="is_rsbsa_registered" class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">RSBSA Registered?</label>
                                <select id="is_rsbsa_registered" name="is_rsbsa_registered" 
                                        x-on:change="isRsbsa = ($el.value === '1')" 
                                        class="w-full text-sm border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="0" {{ old('is_rsbsa_registered', (string)($member->is_rsbsa_registered ?? '0')) === '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('is_rsbsa_registered', (string)($member->is_rsbsa_registered ?? '0')) === '1' ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('is_rsbsa_registered')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- RSBSA Number (Conditional Display) --}}
                            <div>
                                <label for="rsbsa_no" class="block text-[10px] font-bold uppercase mb-2 ml-1" 
                                    :class="{ 'text-gray-400': !isRsbsa, 'text-gray-700': isRsbsa }">
                                    RSBSA Number <span x-show="isRsbsa" class="text-red-500" x-cloak>*</span>
                                </label>
                                <input type="text" 
                                    id="rsbsa_no"
                                    name="rsbsa_no" 
                                    value="{{ old('rsbsa_no', $member->rsbsa_no ?? '') }}" 
                                    placeholder="00-00-00-000-000000" 
                                    :disabled="!isRsbsa"
                                    :required="isRsbsa"
                                    :class="{ 'bg-gray-100 cursor-not-allowed': !isRsbsa, 'bg-white': isRsbsa }"
                                    class="w-full text-sm border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-150">
                                @error('rsbsa_no')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <hr class="border-gray-100">

                    {{-- Section C: Machinery, Specialization & Agro-Practices --}}
                    <section aria-labelledby="section-machinery" x-data="{
                        specialization: @json(old('specialization', $member->specialization ?? '')),
                        hvcdpCategory: @json(old('hvcdp_category', $member->hvcdp_category ?? ''))
                    }">
                        <h4 id="section-machinery" class="text-[11px] font-black text-gray-700 uppercase tracking-wider mb-4">C. Machinery, Skills & Agro-Practices</h4>
                        
                        {{-- 4-H Specialization Dropdown & Dynamic Crop Selection --}}
                        <div class="mb-6 space-y-4">
                            <label for="specialization" class="block text-[10px] font-bold text-gray-400 uppercase ml-1">
                                As a 4-H member, which of the following is your specialization? <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-4">
                                    <div>
                                        <select id="specialization" name="specialization" x-model="specialization" required class="w-full border-gray-200 bg-gray-50 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                            <option value="">Select Specialization</option>
                                            <option value="HVCDP">High Value Crops (HVCDP)</option>
                                            <option value="Livestock">Livestock</option>
                                            <option value="Fisheries">Fisheries</option>
                                            <option value="Rice">Rice</option>
                                            <option value="Corn">Corn</option>
                                            <option value="Poultry">Poultry</option>
                                            <option value="Combination">Combination of Multiple Fields</option>
                                        </select>
                                        @error('specialization')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Conditional HVCDP Category --}}
                                    <div x-show="specialization === 'HVCDP' || specialization === 'Combination'" x-transition x-cloak>
                                        <label for="hvcdp_category" class="block text-[10px] font-bold text-emerald-700 uppercase mb-2 ml-1">HVCDP Category</label>
                                        <select id="hvcdp_category" name="hvcdp_category" x-model="hvcdpCategory" class="w-full border-gray-200 rounded-xl bg-emerald-50/30 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                            <option value="">Select Category</option>
                                            <option value="Vegetables">Vegetables</option>
                                            <option value="Fruits">Fruits</option>
                                            <option value="Industrial Crops">Industrial Crops</option>
                                            <option value="Alternative Staple">Alternative Staple Food</option>
                                            <option value="Cut Across">Cut Across (Multiple)</option>
                                        </select>
                                        @error('hvcdp_category')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Crop Selection Panels --}}
                                <div class="p-6 bg-gray-50/50 rounded-2xl border border-gray-100 min-h-[100px]">
                                    @php
                                        $selectedCrops = old('crops', $member->crops ?? []);
                                        if (is_string($selectedCrops)) {
                                            $selectedCrops = json_decode($selectedCrops, true) ?? [];
                                        }
                                    @endphp

                                    {{-- Vegetables --}}
                                    <div x-show="(specialization === 'HVCDP' || specialization === 'Combination') && (hvcdpCategory === 'Vegetables' || hvcdpCategory === 'Cut Across')" x-transition x-cloak>
                                        <h5 class="text-[10px] font-black text-gray-500 uppercase mb-3">Vegetables & Planting Materials</h5>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(['Mungbean', 'Peanut', 'White Potato', 'Hot Pepper', 'Garlic', 'Shallot', 'Red Onion', 'Ginger', 'Lowland Vegetables', 'Highland Vegetables', 'Malunggay', 'Soybean', 'Adlai', 'Mushroom', 'Bamboo', 'Vanilla', 'Herbs and Spices'] as $veg)
                                                <label class="flex items-center px-3 py-1 bg-white border border-gray-200 rounded-lg text-[11px] cursor-pointer hover:border-emerald-400 transition">
                                                    <input type="checkbox" name="crops[]" value="{{ $veg }}" {{ in_array($veg, (array)$selectedCrops) ? 'checked' : '' }} class="mr-2 text-emerald-600 rounded size-3 focus:ring-emerald-500">
                                                    <span>{{ $veg }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Fruits --}}
                                    <div x-show="(specialization === 'HVCDP' || specialization === 'Combination') && (hvcdpCategory === 'Fruits' || hvcdpCategory === 'Cut Across')" x-transition class="mt-4" x-cloak>
                                        <h5 class="text-[10px] font-black text-gray-500 uppercase mb-3">Fruits</h5>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(['Mango', 'Banana', 'Pineapple', 'Durian', 'Cashew', 'Pili', 'Citrus', 'Strawberry', 'Guyabano', 'Mangosteen', 'Lanzones', 'Rambutan', 'Dragon Fruit', 'Melon/Watermelon'] as $fruit)
                                                <label class="flex items-center px-3 py-1 bg-white border border-gray-200 rounded-lg text-[11px] cursor-pointer hover:border-amber-400 transition">
                                                    <input type="checkbox" name="crops[]" value="{{ $fruit }}" {{ in_array($fruit, (array)$selectedCrops) ? 'checked' : '' }} class="mr-2 text-amber-600 rounded size-3 focus:ring-emerald-500">
                                                    <span>{{ $fruit }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Industrial Crops --}}
                                    <div x-show="(specialization === 'HVCDP' || specialization === 'Combination') && (hvcdpCategory === 'Industrial Crops' || hvcdpCategory === 'Cut Across')" x-transition class="mt-4" x-cloak>
                                        <h5 class="text-[10px] font-black text-gray-500 uppercase mb-3">Industrial Crops</h5>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(['Coffee', 'Cacao', 'Rubber'] as $ind)
                                                <label class="flex items-center px-3 py-1 bg-white border border-gray-200 rounded-lg text-[11px] cursor-pointer hover:border-amber-700 transition">
                                                    <input type="checkbox" name="crops[]" value="{{ $ind }}" {{ in_array($ind, (array)$selectedCrops) ? 'checked' : '' }} class="mr-2 text-amber-800 rounded size-3 focus:ring-emerald-500">
                                                    <span>{{ $ind }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Alternative Staple --}}
                                    <div x-show="(specialization === 'HVCDP' || specialization === 'Combination') && (hvcdpCategory === 'Alternative Staple' || hvcdpCategory === 'Cut Across')" x-transition class="mt-4" x-cloak>
                                        <h5 class="text-[10px] font-black text-gray-500 uppercase mb-3">Alternative Staple</h5>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(['Saba-Banana', 'Sweet Potato', 'Yam', 'Gabi', 'Arrowroot'] as $staple)
                                                <label class="flex items-center px-3 py-1 bg-white border border-gray-200 rounded-lg text-[11px] cursor-pointer hover:border-sky-400 transition">
                                                    <input type="checkbox" name="crops[]" value="{{ $staple }}" {{ in_array($staple, (array)$selectedCrops) ? 'checked' : '' }} class="mr-2 text-sky-600 rounded size-3 focus:ring-emerald-500">
                                                    <span>{{ $staple }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Default Placeholder Message --}}
                                    <div x-show="!hvcdpCategory || (specialization !== 'HVCDP' && specialization !== 'Combination')" class="flex items-center justify-center h-full text-xs italic text-gray-400 min-h-[80px]">
                                        Please select HVCDP or Combination and a category to view specific crops.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Machinery & Skills --}}
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 pt-4 border-t border-gray-100">
                            <div>
                                <span class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Farm Equipment / Technologies Managed</span>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $selectedEquipment = old('farm_equipment', $member->farm_equipment ?? []);
                                        if (is_string($selectedEquipment)) {
                                            $selectedEquipment = json_decode($selectedEquipment, true) ?? [];
                                        }
                                        $equipmentOptions = [
                                            'Tractor / Hand Tractor', 
                                            'Drip Irrigation', 
                                            'Greenhouse / Hydroponics', 
                                            'Drone / Precision Ag', 
                                            'Solar Pump', 
                                            'Processing Machinery', 
                                            'Livestock Feed Mill'
                                        ];
                                    @endphp
                                    @foreach($equipmentOptions as $equip)
                                        <label class="flex items-center px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs cursor-pointer hover:border-emerald-400 transition">
                                            <input type="checkbox" name="farm_equipment[]" value="{{ $equip }}" 
                                                {{ in_array($equip, (array)$selectedEquipment) ? 'checked' : '' }} 
                                                class="mr-2 text-emerald-600 rounded size-3 focus:ring-emerald-500">
                                            <span>{{ $equip }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('farm_equipment')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="agri_skills" class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Specialized Agricultural Skills</label>
                                <textarea id="agri_skills" name="agri_skills" rows="3" placeholder="e.g. Grafting, Insemination, Organic Fertilizer Preparation, Financial Record Keeping..." class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 text-sm">{{ old('agri_skills', $member->agri_skills ?? '') }}</textarea>
                                @error('agri_skills')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <hr class="border-gray-100">

                    {{-- Section D: Certifications, Achievements & Resume Attachments --}}
                    <section aria-labelledby="section-certifications">
                        <h4 id="section-certifications" class="text-[11px] font-black text-gray-700 uppercase tracking-wider mb-4">D. Certifications & Supporting Documents</h4>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="certifications" class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">NC Certifications (e.g., TESDA NC II Organic Agriculture)</label>
                                <input type="text" id="certifications" name="certifications" value="{{ old('certifications', $member->certifications ?? '') }}" placeholder="e.g. Agricultural Crops Production NC II, Animal Production NC II" class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                @error('certifications')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="member_file" class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Upload Agri-Resume / Curriculum Vitae (PDF/Docx)</label>
                                <input type="file" id="member_file" name="member_file" accept=".pdf,.doc,.docx" class="w-full text-xs text-gray-500 border border-gray-200 rounded-2xl file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                                @if($member->agri_resume_path)
                                    <p class="mt-1.5 text-[11px] font-semibold text-emerald-700">
                                        <i class="mr-1 fas fa-paperclip"></i>
                                        A resume is already on file. Uploading a new one replaces it.
                                    </p>
                                @endif
                                @error('member_file')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            {{-- Submit Action Bar --}}
            <div class="flex justify-end pt-2">
                <button type="submit" class="px-8 py-3 bg-emerald-600 text-white font-bold text-sm rounded-2xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-600/20">
                    Save Member Details
                </button>
            </div>
        </form>
    </div>

    {{-- Show only the provinces and organizations of the chosen region --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const regionSelect = document.getElementById('region_id');
            if (!regionSelect) return;

            function filterByRegion(select) {
                if (!select) return;
                const region = regionSelect.value;

                select.querySelectorAll('option').forEach(option => {
                    if (!option.value) return; // keep the "Select ..." placeholder

                    const optionRegion = option.getAttribute('data-region-id');
                    const show = !region || !optionRegion || optionRegion === region;

                    option.hidden = !show;
                    option.disabled = !show;
                    if (!show && option.selected) select.value = '';
                });
            }

            function filterAll() {
                filterByRegion(document.getElementById('province_id'));
                filterByRegion(document.getElementById('organization_id'));
            }

            regionSelect.addEventListener('change', filterAll);
            filterAll(); // initial run on page load
        });
    </script>
</x-app-layout>