<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Edit Member Profile') }}
                </h2>
                <p class="mt-0.5 text-xs text-gray-500">Update account information, demographics, and agricultural profile.</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('profile.show') }}" 
                   class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase bg-white border border-gray-200 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Cancel / Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/50 min-h-screen">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
        
        {{-- PAGE HEADER --}}
        <div class="md:flex md:items-center md:justify-between px-4 sm:px-0">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">Edit Member Profile</h2>
                <p class="mt-1 text-sm text-slate-500">Update demographic, regional, and agricultural experience information.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- SECTION I: IDENTITY & DEMOGRAPHICS --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-6 bg-emerald-50/40 border-b border-emerald-100/60 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2.5 bg-emerald-500 text-white rounded-xl shadow-sm shadow-emerald-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Identity & Demographics</h3>
                            <p class="text-xs text-slate-500">Personal identification and primary contact information</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                        Step 1 of 4
                    </span>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label for="first_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">First Name <span class="text-emerald-500">*</span></label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $member?->first_name) }}" required class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('first_name') <span class="text-xs text-rose-500 mt-1.5 flex items-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="middle_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $member?->middle_name) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('middle_name') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Last Name <span class="text-emerald-500">*</span></label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $member?->last_name) }}" required class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('last_name') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="suffix" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Suffix</label>
                        <input type="text" id="suffix" name="suffix" placeholder="e.g. Jr., III" value="{{ old('suffix', $member?->suffix) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('suffix') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="dob" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Date of Birth <span class="text-emerald-500">*</span></label>
                        <input type="date" id="dob" name="dob" value="{{ old('dob', $member?->dob ? $member->dob->format('Y-m-d') : '') }}" required class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('dob') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="sex" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Sex <span class="text-emerald-500">*</span></label>
                        <select id="sex" name="sex" required class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 rounded-xl text-sm font-medium transition duration-150">
                            <option value="">Select Sex</option>
                            <option value="male" {{ old('sex', $member?->sex) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('sex', $member?->sex) == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('sex') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="civil_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Civil Status <span class="text-emerald-500">*</span></label>
                        <select id="civil_status" name="civil_status" required class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 rounded-xl text-sm font-medium transition duration-150">
                            <option value="">Select Status</option>
                            <option value="single" {{ old('civil_status', $member?->civil_status) == 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married" {{ old('civil_status', $member?->civil_status) == 'married' ? 'selected' : '' }}>Married</option>
                            <option value="widowed" {{ old('civil_status', $member?->civil_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="separated" {{ old('civil_status', $member?->civil_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                        @error('civil_status') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="contact_no" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Contact Number <span class="text-emerald-500">*</span></label>
                        <input type="text" id="contact_no" name="contact_no" value="{{ old('contact_no', $member?->contact_no) }}" required class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('contact_no') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Address <span class="text-emerald-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('email') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION II: ADDRESS & LOCATION DETAILS --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-6 bg-sky-50/40 border-b border-sky-100/60 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2.5 bg-sky-500 text-white rounded-xl shadow-sm shadow-sky-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Address & Location Details</h3>
                            <p class="text-xs text-slate-500">Geographic mapping and residential region setup</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-800">
                        Step 2 of 4
                    </span>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="region_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Region</label>
                        <select id="region_id" name="region_id" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-sky-500 focus:ring-sky-500/20 rounded-xl text-sm font-medium transition duration-150">
                            <option value="">Select Region</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ old('region_id', $member?->region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                        @error('region_id') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="province_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Province</label>
                        <select id="province_id" name="province_id" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-sky-500 focus:ring-sky-500/20 rounded-xl text-sm font-medium transition duration-150">
                            <option value="">Select Province</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}" {{ old('province_id', $member?->province_id) == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                            @endforeach
                        </select>
                        @error('province_id') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="city_municipality" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">City / Municipality</label>
                        <input type="text" id="city_municipality" name="city_municipality" value="{{ old('city_municipality', $member?->city_municipality) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-sky-500 focus:ring-sky-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('city_municipality') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="district" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">District</label>
                        <input type="text" id="district" name="district" value="{{ old('district', $member?->district) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-sky-500 focus:ring-sky-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('district') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="barangay" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Barangay</label>
                        <input type="text" id="barangay" name="barangay" value="{{ old('barangay', $member?->barangay) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-sky-500 focus:ring-sky-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('barangay') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="zip_code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Zip Code</label>
                        <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code', $member?->zip_code) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-sky-500 focus:ring-sky-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('zip_code') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION III: PROFESSIONAL & PROGRAM DETAILS --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-6 bg-purple-50/40 border-b border-purple-100/60 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2.5 bg-purple-500 text-white rounded-xl shadow-sm shadow-purple-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Professional & Program Details</h3>
                            <p class="text-xs text-slate-500">Cooperative affiliation, courses, and crop specialization</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                        Step 3 of 4
                    </span>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="member_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Member Type</label>
                        <input type="text" id="member_type" name="member_type" value="{{ old('member_type', $member?->member_type) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-purple-500 focus:ring-purple-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('member_type') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="occupation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Occupation</label>
                        <input type="text" id="occupation" name="occupation" value="{{ old('occupation', $member?->occupation) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-purple-500 focus:ring-purple-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('occupation') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="organization_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Organization / Coop</label>
                        <select id="organization_id" name="organization_id" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-purple-500 focus:ring-purple-500/20 rounded-xl text-sm font-medium transition duration-150">
                            <option value="">Select Organization</option>
                            @if(isset($organizations))
                                @foreach($organizations as $org)
                                    <option value="{{ $org->id }}" {{ old('organization_id', $member?->organization_id) == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('organization_id') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="specialization" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Specialization</label>
                        <input type="text" id="specialization" name="specialization" value="{{ old('specialization', $member?->specialization) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-purple-500 focus:ring-purple-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('specialization') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="hvcdp_category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">HVCDP Category</label>
                        <input type="text" id="hvcdp_category" name="hvcdp_category" value="{{ old('hvcdp_category', $member?->hvcdp_category) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-purple-500 focus:ring-purple-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('hvcdp_category') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="internship" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Internship</label>
                        <input type="text" id="internship" name="internship" value="{{ old('internship', $member?->internship) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-purple-500 focus:ring-purple-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('internship') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="scholarship" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Scholarship</label>
                        <input type="text" id="scholarship" name="scholarship" value="{{ old('scholarship', $member?->scholarship) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-purple-500 focus:ring-purple-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('scholarship') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="lsa_level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">LSA Level</label>
                        <input type="text" id="lsa_level" name="lsa_level" value="{{ old('lsa_level', $member?->lsa_level) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-purple-500 focus:ring-purple-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('lsa_level') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="lsa_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">LSA Type</label>
                        <input type="text" id="lsa_type" name="lsa_type" value="{{ old('lsa_type', $member?->lsa_type) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-purple-500 focus:ring-purple-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('lsa_type') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-3">
                        <label for="training_course" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Training Course</label>
                        <input type="text" id="training_course" name="training_course" value="{{ old('training_course', $member?->training_course) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-purple-500 focus:ring-purple-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('training_course') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-3">
                        <label for="crops_input" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Crops Produced <span class="text-xs font-normal text-slate-400 capitalize">(Comma-separated)</span></label>
                        <input type="text" id="crops_input" name="crops_input" value="{{ old('crops_input', is_array($member?->crops) ? implode(', ', $member->crops) : $member?->crops) }}" placeholder="e.g. Rice, Corn, Banana" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-purple-500 focus:ring-purple-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('crops_input') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-3">
                        <label for="services_input" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Services Provided <span class="text-xs font-normal text-slate-400 capitalize">(Comma-separated)</span></label>
                        <input type="text" id="services_input" name="services_input" value="{{ old('services_input', is_array($member?->services) ? implode(', ', $member->services) : $member?->services) }}" placeholder="e.g. Milling, Processing, Transport" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-purple-500 focus:ring-purple-500/20 rounded-xl text-sm font-medium transition duration-150">
                        @error('services_input') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION IV: AGRI-RESUME & FIELD EXPERIENCE DETAILS --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-6 bg-amber-50/40 border-b border-amber-100/60 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2.5 bg-amber-500 text-white rounded-xl shadow-sm shadow-amber-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Agri-Resume & Field Experience Details</h3>
                            <p class="text-xs text-slate-500">Academic degree, land profile, and specialized skills</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                        Step 4 of 4
                    </span>
                </div>

                <div class="p-6 space-y-8">
                    {{-- Subsection A --}}
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">A. Educational Background</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="highest_education" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Highest Education</label>
                                <input type="text" id="highest_education" name="highest_education" value="{{ old('highest_education', $member?->highest_education) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-amber-500/20 rounded-xl text-sm font-medium transition duration-150">
                                @error('highest_education') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="degree_course" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Degree Course</label>
                                <input type="text" id="degree_course" name="degree_course" value="{{ old('degree_course', $member?->degree_course) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-amber-500/20 rounded-xl text-sm font-medium transition duration-150">
                                @error('degree_course') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="school_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">School / Institution</label>
                                <input type="text" id="school_name" name="school_name" value="{{ old('school_name', $member?->school_name) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-amber-500/20 rounded-xl text-sm font-medium transition duration-150">
                                @error('school_name') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-6">
                        {{-- Subsection B --}}
                        <div class="flex items-center space-x-2 mb-4">
                            <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">B. Farm Profile & RSBSA Details</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label for="farm_area" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Farm Area (Hectares)</label>
                                <input type="number" step="0.01" id="farm_area" name="farm_area" value="{{ old('farm_area', $member?->farm_area) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-amber-500/20 rounded-xl text-sm font-medium transition duration-150">
                                @error('farm_area') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="land_ownership" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Land Ownership Type</label>
                                <input type="text" id="land_ownership" name="land_ownership" value="{{ old('land_ownership', $member?->land_ownership) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-amber-500/20 rounded-xl text-sm font-medium transition duration-150">
                                @error('land_ownership') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Modern Card Checkbox for RSBSA --}}
                            <div class="flex items-end">
                                <label class="w-full relative flex items-center p-3 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition duration-150 select-none">
                                    <input type="checkbox" id="is_rsbsa_registered" name="is_rsbsa_registered" value="1" {{ old('is_rsbsa_registered', $member?->is_rsbsa_registered) ? 'checked' : '' }} class="rounded border-slate-300 text-amber-600 focus:ring-amber-500 h-4 w-4">
                                    <span class="ml-3 text-xs font-bold text-slate-700 uppercase tracking-wider">RSBSA Registered</span>
                                </label>
                                @error('is_rsbsa_registered') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="rsbsa_no" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">RSBSA Number</label>
                                <input type="text" id="rsbsa_no" name="rsbsa_no" value="{{ old('rsbsa_no', $member?->rsbsa_no) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-amber-500/20 rounded-xl text-sm font-medium transition duration-150">
                                @error('rsbsa_no') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-6">
                        {{-- Subsection C --}}
                        <div class="flex items-center space-x-2 mb-4">
                            <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">C. Skills, Equipment & Resume Summary</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="farm_equipment" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Farm Equipment</label>
                                <input type="text" id="farm_equipment" name="farm_equipment" value="{{ is_array(old('farm_equipment', $member?->farm_equipment)) ? implode(', ', old('farm_equipment', $member?->farm_equipment)) : old('farm_equipment', $member?->farm_equipment) }}" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-amber-500/20 rounded-xl text-sm font-medium transition duration-150">
                                @error('farm_equipment') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="agri_skills" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Agricultural Skills</label>
                                <input type="text" id="agri_skills" name="agri_skills" value="{{ old('agri_skills', $member?->agri_skills) }}" placeholder="e.g. Grafting, Soil Testing" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-amber-500/20 rounded-xl text-sm font-medium transition duration-150">
                                @error('agri_skills') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="certifications" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Certifications</label>
                                <input type="text" id="certifications" name="certifications" value="{{ old('certifications', $member?->certifications) }}" placeholder="e.g. NC II Organic Agriculture" class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-amber-500/20 rounded-xl text-sm font-medium transition duration-150">
                                @error('certifications') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-3">
                                <label for="bio_summary" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bio / Profile Summary</label>
                                <textarea id="bio_summary" name="bio_summary" rows="4" placeholder="Brief overview of agricultural background, focus areas, and key achievements..." class="w-full bg-slate-50/50 text-slate-900 border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-amber-500/20 rounded-xl text-sm font-medium transition duration-150">{{ old('bio_summary', $member?->bio_summary) }}</textarea>
                                @error('bio_summary') <span class="text-xs text-rose-500 mt-1.5 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTION BAR --}}
            <div class="sticky bottom-4 z-10 p-4 bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200 shadow-xl flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 hidden sm:inline">Ensure all required fields (<span class="text-emerald-500">*</span>) are filled out correctly.</span>
                <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('profile.show') }}" class="px-5 py-2.5 text-xs font-bold tracking-wider text-slate-600 uppercase transition duration-150 ease-in-out bg-white border border-slate-200 rounded-xl hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400">
                        Cancel
                    </a>

                    <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 text-xs font-bold tracking-wider text-white uppercase transition duration-150 ease-in-out bg-emerald-600 border border-transparent rounded-xl hover:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Profile
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
</x-app-layout>