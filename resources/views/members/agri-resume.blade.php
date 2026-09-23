<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Agri-Resume Profile') }}
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-1">
                    Manage your complete agricultural credentials and download your standardized professional CV.
                </p>
            </div>
            
            {{-- Action Buttons: Download PDF & Download ODF / Print Options --}}
            @if(auth()->user()->role === 'Member')
                <div class="flex items-center gap-2">
                    <a href="{{ route('member.agri-resume.download', ['format' => 'odf']) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 active:bg-black text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md hover:shadow-lg transition-all duration-150 transform hover:-translate-y-0.5">
                        <i class="fas fa-file-word text-sm text-blue-400"></i>
                        <span>Download ODF CV</span>
                    </a>

                    <a href="{{ route('member.agri-resume.download', ['format' => 'pdf']) }}" 
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md hover:shadow-lg transition-all duration-150 transform hover:-translate-y-0.5">
                        <i class="fas fa-file-pdf text-sm"></i>
                        <span>Download PDF CV</span>
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/50 min-h-screen" x-data="{ editing: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Session Notifications --}}
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 flex items-center gap-3 shadow-sm" role="alert">
                    <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-xs"></i>
                    </div>
                    <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Main Grid Container --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- Left Column: Live Card Preview --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden">
                        {{-- Background Accent --}}
                        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-36 h-36 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

                        {{-- Header Profile Card --}}
                        <div class="text-center pb-6 border-b border-slate-100 relative">
                            <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-black text-2xl shadow-lg shadow-emerald-500/30 mb-4">
                                {{ strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1)) }}
                            </div>
                            <h3 class="text-xl font-black text-slate-800 tracking-tight">
                                {{ $member->first_name }} {{ $member->middle_name ? $member->middle_name . ' ' : '' }}{{ $member->last_name }} {{ $member->extension_name }}
                            </h3>
                            <p class="text-xs font-bold text-slate-400 mt-0.5 font-mono">
                                ID: {{ $member->member_id }}
                            </p>

                            <div class="mt-3 flex flex-wrap justify-center gap-1.5">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                    <i class="fas fa-leaf text-[9px]"></i>
                                    {{ $member->specialization ?? 'General Agriculture' }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700">
                                    <i class="fas fa-briefcase text-[9px]"></i>
                                    {{ $member->occupation ?? 'Farmer / Agri Professional' }}
                                </span>
                            </div>
                        </div>

                        {{-- Key Agri Stats Highlights --}}
                        <div class="grid grid-cols-2 gap-3 py-4 my-2 bg-slate-50 rounded-2xl p-3 border border-slate-100 text-center">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-wider text-slate-400">Experience</p>
                                <p class="text-sm font-black text-slate-800 mt-0.5">{{ $member->years_experience ?? 0 }} Years</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-wider text-slate-400">Farm Size</p>
                                <p class="text-sm font-black text-slate-800 mt-0.5">{{ $member->farm_area ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Contact Details Stack --}}
                        <div class="py-4 space-y-3.5 text-xs">
                            <div class="flex items-center gap-3 text-slate-600">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="truncate">
                                    <p class="text-[10px] uppercase font-black text-slate-400">Education</p>
                                    <p class="font-bold text-slate-700 truncate">{{ $member->highest_education ?? 'Not specified' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-slate-600">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="truncate">
                                    <p class="text-[10px] uppercase font-black text-slate-400">Email Address</p>
                                    <p class="font-bold text-slate-700 truncate">{{ $member->email }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-slate-600">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-black text-slate-400">Phone Contact</p>
                                    <p class="font-bold text-slate-700">{{ $member->contact_no ?? 'Not specified' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-slate-600">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-black text-slate-400">Location</p>
                                    <p class="font-bold text-slate-700">
                                        {{ $member->barangay ?? 'N/A' }}, {{ $member->city_municipality ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Summary Preview --}}
                        @if($member->bio_summary)
                            <div class="pt-3 border-t border-slate-100">
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Agri Bio / Profile</p>
                                <p class="text-xs text-slate-600 italic line-clamp-3">"{{ $member->bio_summary }}"</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Column: Details & Edit Form Container --}}
                <div class="lg:col-span-8">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/50 border border-slate-100">
                        
                        {{-- Header Row with Toggle Button --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 mb-6 border-b border-slate-100">
                            <div>
                                <h3 class="text-lg font-black text-slate-800" x-text="editing ? 'Edit Professional Information' : 'Member Profile Details'">
                                    Member Profile Details
                                </h3>
                                <p class="text-xs font-semibold text-slate-500 mt-0.5">
                                    <span x-show="!editing">Review member's complete registered agricultural credentials and personal profile.</span>
                                    <span x-show="editing" x-cloak>Update all details to sync with your generated Agri-Resume documents.</span>
                                </p>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest border border-emerald-200/60 hidden sm:inline-block">
                                    Live Sync
                                </span>

                                {{-- Edit / Cancel Toggle Button --}}
                                <button type="button" @click="editing = !editing" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-900 active:bg-black text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all shadow-md">
                                    <i class="fas" :class="editing ? 'fa-times' : 'fa-user-edit'"></i>
                                    <span x-text="editing ? 'Cancel' : 'Edit Profile'">Edit Profile</span>
                                </button>
                            </div>
                        </div>

                        {{-- READ-ONLY DETAILS VIEW MODE --}}
                        <div x-show="!editing" class="space-y-6">
                            {{-- System Info --}}
                            <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-100 space-y-3">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Verified System Details</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Member ID</p>
                                        <p class="font-bold text-slate-800 font-mono">{{ $member->member_id }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">RSBSA / Agri Ref No.</p>
                                        <p class="font-bold text-slate-800 font-mono">{{ $member->rsbsa_no ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Email Address</p>
                                        <p class="font-bold text-slate-800 truncate">{{ $member->email }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Personal Demographics --}}
                            <div class="space-y-3">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Personal & Demographic Details</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                    <div class="sm:col-span-3">
                                        <p class="text-xs font-bold text-slate-400">Full Name</p>
                                        <p class="font-bold text-slate-800">
                                            {{ $member->first_name }} {{ $member->middle_name }} {{ $member->last_name }} {{ $member->extension_name }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Date of Birth</p>
                                        <p class="font-bold text-slate-700">{{ $member->birth_date ? \Carbon\Carbon::parse($member->birth_date)->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Gender</p>
                                        <p class="font-bold text-slate-700">{{ $member->gender ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Civil Status</p>
                                        <p class="font-bold text-slate-700">{{ $member->civil_status ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Professional & Contact Info --}}
                            <div class="space-y-3 pt-2">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Contact & Professional Background</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Contact Number</p>
                                        <p class="font-bold text-slate-700">{{ $member->contact_no ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Current Role / Occupation</p>
                                        <p class="font-bold text-slate-700">{{ $member->occupation ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Educational Attainment</p>
                                        <p class="font-bold text-slate-700">{{ $member->highest_education ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Cooperative / Association</p>
                                        <p class="font-bold text-slate-700">{{ $member->organization_name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Agricultural Metrics --}}
                            <div class="space-y-3 pt-2">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Agricultural Operations</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Specialization</p>
                                        <p class="font-bold text-slate-700">{{ $member->specialization ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Experience</p>
                                        <p class="font-bold text-slate-700">{{ $member->years_experience ?? 0 }} Years</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Farm Size</p>
                                        <p class="font-bold text-slate-700">{{ $member->farm_area ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Tenure / Ownership</p>
                                        <p class="font-bold text-slate-700">{{ $member->land_ownership ?? 'N/A' }}</p>
                                    </div>
                                    <div class="sm:col-span-2 lg:col-span-4">
                                        <p class="text-xs font-bold text-slate-400">Main Commodities / Crops Raised</p>
                                        <p class="font-bold text-slate-700">{{ $member->primary_commodities ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Location Details --}}
                            <div class="space-y-3 pt-2">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Location & Address</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                    <div class="sm:col-span-2">
                                        <p class="text-xs font-bold text-slate-400">Street / House No. / Sitio</p>
                                        <p class="font-bold text-slate-700">{{ $member->street_address ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Barangay</p>
                                        <p class="font-bold text-slate-700">{{ $member->barangay ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">City / Municipality</p>
                                        <p class="font-bold text-slate-700">{{ $member->city_municipality ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Province</p>
                                        <p class="font-bold text-slate-700">{{ $member->province ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Zip Code</p>
                                        <p class="font-bold text-slate-700">{{ $member->zip_code ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Emergency Contact --}}
                            <div class="space-y-3 pt-2">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Emergency Contact Person</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Contact Name</p>
                                        <p class="font-bold text-slate-700">{{ $member->emergency_contact_name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Relationship</p>
                                        <p class="font-bold text-slate-700">{{ $member->emergency_contact_relation ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Emergency Phone</p>
                                        <p class="font-bold text-slate-700">{{ $member->emergency_contact_no ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Bio Summary --}}
                            <div class="space-y-2 pt-2">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Bio / Professional Summary</h4>
                                <p class="text-sm font-semibold text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-100 leading-relaxed">
                                    {{ $member->bio_summary ?? 'No professional summary provided.' }}
                                </p>
                            </div>
                        </div>

                        {{-- EDIT FORM MODE --}}
                        <form x-show="editing" x-cloak method="post" action="{{ route('member.agri-resume.update') }}" class="space-y-6">
                            @csrf
                            @method('put')

                            {{-- Read-Only System Details --}}
                            <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-100 space-y-4">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Verified System Details (Read-Only)</h4>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="member_id" :value="__('Member ID')" class="text-xs font-bold text-slate-600" />
                                        <x-text-input id="member_id" type="text" class="mt-1 block w-full bg-slate-100/80 text-slate-500 border-slate-200 font-semibold font-mono cursor-not-allowed" value="{{ $member->member_id }}" disabled />
                                    </div>
                                    <div>
                                        <x-input-label for="rsbsa_no" :value="__('RSBSA / Agri Ref No.')" class="text-xs font-bold text-slate-600" />
                                        <x-text-input id="rsbsa_no" name="rsbsa_no" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-mono" :value="old('rsbsa_no', $member->rsbsa_no)" placeholder="e.g. 03-14-02-000-000000" />
                                        <x-input-error class="mt-2" :messages="$errors->get('rsbsa_no')" />
                                    </div>
                                    <div>
                                        <x-input-label for="email" :value="__('Email Address')" class="text-xs font-bold text-slate-600" />
                                        <x-text-input id="email" type="text" class="mt-1 block w-full bg-slate-100/80 text-slate-500 border-slate-200 font-semibold cursor-not-allowed" value="{{ $member->email }}" disabled />
                                    </div>
                                </div>
                            </div>

                            {{-- Editable Personal Demographics --}}
                            <div class="space-y-4">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Personal & Demographic Details</h4>

                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                                    <div>
                                        <x-input-label for="first_name" :value="__('First Name')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('first_name', $member->first_name)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                                    </div>

                                    <div>
                                        <x-input-label for="middle_name" :value="__('Middle Name')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('middle_name', $member->middle_name)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
                                    </div>

                                    <div>
                                        <x-input-label for="last_name" :value="__('Last Name')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('last_name', $member->last_name)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                                    </div>

                                    <div>
                                        <x-input-label for="extension_name" :value="__('Suffix (Jr., III)')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="extension_name" name="extension_name" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('extension_name', $member->extension_name)" placeholder="e.g. Jr." />
                                        <x-input-error class="mt-2" :messages="$errors->get('extension_name')" />
                                    </div>

                                    <div>
                                        <x-input-label for="birth_date" :value="__('Date of Birth')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm" :value="old('birth_date', $member->birth_date)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                                    </div>

                                    <div>
                                        <x-input-label for="gender" :value="__('Gender')" class="text-xs font-bold text-slate-700" />
                                        <select id="gender" name="gender" class="mt-1 block w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm text-sm font-semibold text-slate-700">
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ old('gender', $member->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender', $member->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Prefer not to say" {{ old('gender', $member->gender) == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <x-input-label for="civil_status" :value="__('Civil Status')" class="text-xs font-bold text-slate-700" />
                                        <select id="civil_status" name="civil_status" class="mt-1 block w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm text-sm font-semibold text-slate-700">
                                            <option value="">Select Civil Status</option>
                                            <option value="Single" {{ old('civil_status', $member->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                            <option value="Married" {{ old('civil_status', $member->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                            <option value="Widowed" {{ old('civil_status', $member->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                            <option value="Separated" {{ old('civil_status', $member->civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('civil_status')" />
                                    </div>
                                </div>
                            </div>

                            {{-- Editable Professional & Contact Details --}}
                            <div class="space-y-4 pt-2">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Contact & Background</h4>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="contact_no" :value="__('Contact Number')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="contact_no" name="contact_no" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('contact_no', $member->contact_no)" placeholder="e.g. 09123456789" />
                                        <x-input-error class="mt-2" :messages="$errors->get('contact_no')" />
                                    </div>

                                    <div>
                                        <x-input-label for="occupation" :value="__('Current Role / Occupation')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="occupation" name="occupation" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('occupation', $member->occupation)" placeholder="e.g. Rice Farmer / Agronomist" />
                                        <x-input-error class="mt-2" :messages="$errors->get('occupation')" />
                                    </div>

                                    <div>
                                        <x-input-label for="highest_education" :value="__('Educational Attainment')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="highest_education" name="highest_education" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('highest_education', $member->highest_education)" placeholder="e.g. BS Agriculture" />
                                        <x-input-error class="mt-2" :messages="$errors->get('highest_education')" />
                                    </div>

                                    <div>
                                        <x-input-label for="organization_name" :value="__('Cooperative / Association')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="organization_name" name="organization_name" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('organization_name', $member->organization_name)" placeholder="e.g. Local Farmers Coop" />
                                        <x-input-error class="mt-2" :messages="$errors->get('organization_name')" />
                                    </div>
                                </div>
                            </div>

                            {{-- Editable Agricultural Metrics --}}
                            <div class="space-y-4 pt-2">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Agricultural Operations</h4>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <x-input-label for="specialization" :value="__('Specialization')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="specialization" name="specialization" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('specialization', $member->specialization)" placeholder="e.g. Crop Production" />
                                        <x-input-error class="mt-2" :messages="$errors->get('specialization')" />
                                    </div>

                                    <div>
                                        <x-input-label for="years_experience" :value="__('Years of Experience')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="years_experience" name="years_experience" type="number" min="0" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('years_experience', $member->years_experience)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('years_experience')" />
                                    </div>

                                    <div>
                                        <x-input-label for="farm_area" :value="__('Farm Size')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="farm_area" name="farm_area" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('farm_area', $member->farm_area)" placeholder="e.g. 2.5 Hectares" />
                                        <x-input-error class="mt-2" :messages="$errors->get('farm_area')" />
                                    </div>

                                    <div>
                                        <x-input-label for="land_ownership" :value="__('Tenure / Ownership')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="land_ownership" name="land_ownership" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('land_ownership', $member->land_ownership)" placeholder="e.g. Owner / Tenant" />
                                        <x-input-error class="mt-2" :messages="$errors->get('land_ownership')" />
                                    </div>

                                    <div class="sm:col-span-2 lg:col-span-4">
                                        <x-input-label for="primary_commodities" :value="__('Main Commodities / Crops Raised')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="primary_commodities" name="primary_commodities" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('primary_commodities', $member->primary_commodities)" placeholder="e.g. Rice, Corn, High Value Crops" />
                                        <x-input-error class="mt-2" :messages="$errors->get('primary_commodities')" />
                                    </div>
                                </div>
                            </div>

                            {{-- Editable Location Details --}}
                            <div class="space-y-4 pt-2">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Location & Address</h4>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div class="sm:col-span-2">
                                        <x-input-label for="street_address" :value="__('Street / House No. / Sitio')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="street_address" name="street_address" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('street_address', $member->street_address)" placeholder="e.g. Sitio Maligaya" />
                                        <x-input-error class="mt-2" :messages="$errors->get('street_address')" />
                                    </div>

                                    <div>
                                        <x-input-label for="barangay" :value="__('Barangay')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="barangay" name="barangay" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('barangay', $member->barangay)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('barangay')" />
                                    </div>

                                    <div>
                                        <x-input-label for="city_municipality" :value="__('City / Municipality')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="city_municipality" name="city_municipality" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('city_municipality', $member->city_municipality)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('city_municipality')" />
                                    </div>

                                    <div>
                                        <x-input-label for="province" :value="__('Province')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="province" name="province" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('province', $member->province)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('province')" />
                                    </div>

                                    <div>
                                        <x-input-label for="zip_code" :value="__('Zip Code')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="zip_code" name="zip_code" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('zip_code', $member->zip_code)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('zip_code')" />
                                    </div>
                                </div>
                            </div>

                            {{-- Editable Emergency Contact --}}
                            <div class="space-y-4 pt-2">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Emergency Contact Person</h4>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="emergency_contact_name" :value="__('Contact Name')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="emergency_contact_name" name="emergency_contact_name" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('emergency_contact_name', $member->emergency_contact_name)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_name')" />
                                    </div>

                                    <div>
                                        <x-input-label for="emergency_contact_relation" :value="__('Relationship')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="emergency_contact_relation" name="emergency_contact_relation" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('emergency_contact_relation', $member->emergency_contact_relation)" placeholder="e.g. Spouse / Parent" />
                                        <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_relation')" />
                                    </div>

                                    <div>
                                        <x-input-label for="emergency_contact_no" :value="__('Emergency Phone')" class="text-xs font-bold text-slate-700" />
                                        <x-text-input id="emergency_contact_no" name="emergency_contact_no" type="text" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" :value="old('emergency_contact_no', $member->emergency_contact_no)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_no')" />
                                    </div>
                                </div>
                            </div>

                            {{-- Editable Bio Summary --}}
                            <div class="space-y-2 pt-2">
                                <x-input-label for="bio_summary" :value="__('Bio / Professional Summary')" class="text-xs font-bold text-slate-700" />
                                <textarea id="bio_summary" name="bio_summary" rows="4" class="mt-1 block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm p-3" placeholder="Provide a brief summary of your agricultural background, expertise, and goals...">{{ old('bio_summary', $member->bio_summary) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('bio_summary')" />
                            </div>

                            {{-- Form Submission Controls --}}
                            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                                <button type="button" @click="editing = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                                    Cancel
                                </button>
                                <x-primary-button class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 rounded-xl font-bold text-xs uppercase tracking-wider shadow-md">
                                    {{ __('Save Profile Changes') }}
                                </x-primary-button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>