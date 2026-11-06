<x-app-layout>
    @php
        // Shared by "My Profile" (member.profile.show) and the admin view of a member
        $isSelf    = request()->routeIs('member.profile.*');
        $editUrl   = $isSelf ? route('member.profile.edit') : route('members.edit', $member->id);

        $photoUrl  = $member->photo_path ? asset('storage/' . $member->photo_path) : null;
        $initials  = mb_strtoupper(mb_substr($member->first_name ?? '', 0, 1) . mb_substr($member->last_name ?? '', 0, 1));

        $missing   = $member->missingIdFields();
        $canPrint  = empty($missing);
        $total     = count(\App\Models\Member::ID_REQUIRED_FIELDS);
        $done      = $total - count($missing);
        $percent   = $total ? (int) round($done / $total * 100) : 100;

        // Dynamic styling based on LSA Level
        $tier = $member->lsa_level ?? 'Standard';
        $gradient = match ($tier) {
            'Platinum' => 'from-slate-800 to-slate-950',
            'Gold'     => 'from-amber-400 to-amber-600',
            default    => 'from-emerald-500 to-emerald-700',
        };
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">{{ $isSelf ? 'My Digital ID & Record' : 'Member Digital ID & Record' }}</h2>
            @unless($isSelf)
                <a href="{{ route('members.index') }}" class="text-sm font-bold transition-colors text-emerald-600 hover:text-emerald-800">
                    &larr; Back to Directory
                </a>
            @endunless
        </div>
    </x-slot>

    {{-- Block browser printing (Ctrl + P): IDs must come from the official Print ID card --}}
    <style>
        .print-blocked { display: none; }
        @media print {
            body * { visibility: hidden !important; }
            .print-blocked, .print-blocked * { visibility: visible !important; }
            .print-blocked {
                display: block !important;
                position: fixed; inset: 0;
                padding: 3rem; text-align: center;
                font-family: sans-serif;
            }
        }
    </style>
    <div class="print-blocked">
        <h1 style="font-size: 20px; font-weight: 800;">This page can't be printed.</h1>
        <p style="margin-top: 8px; font-size: 13px;">
            @if($canPrint)
                Use the <strong>Print ID</strong> button on the page to print the official ID card.
            @else
                The ID card is available once the member profile is complete
                ({{ count($missing) }} required {{ \Illuminate\Support\Str::plural('detail', count($missing)) }} missing).
            @endif
        </p>
    </div>

    <div class="max-w-7xl px-4 py-8 mx-auto">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 mb-6 text-xs font-bold border rounded-2xl bg-emerald-50 border-emerald-100 text-emerald-700">
                <i class="fas fa-circle-check text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-start gap-3 p-4 mb-6 text-xs font-bold border rounded-2xl bg-amber-50 border-amber-200 text-amber-800">
                <i class="mt-0.5 fas fa-triangle-exclamation text-amber-600"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Two Column Grid: Left ID Card, Right Member Details --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- LEFT COLUMN: ID Card & Quick Actions --}}
            <div class="lg:col-span-5 xl:col-span-4 space-y-6 lg:sticky lg:top-8">
                {{-- ID Card Container --}}
                <div class="id-card bg-white rounded-[2.5rem] shadow-2xl shadow-emerald-100 border border-gray-100 overflow-hidden relative">

                    {{-- Top Accent / Tier Color --}}
                    <div class="flex items-start justify-between h-32 p-6 bg-gradient-to-br {{ $gradient }}">
                        <span class="text-white/80 text-[10px] font-black uppercase tracking-[0.2em]">Official Member ID</span>

                        {{-- Tier Badge --}}
                        <div class="px-3 py-1 border rounded-full bg-white/20 backdrop-blur-md border-white/30">
                            <span class="text-white text-[10px] font-bold uppercase tracking-wider">
                                {{ $tier }}
                            </span>
                        </div>
                    </div>

                    {{-- Profile Section --}}
                    <div class="px-8 -mt-12 text-center">
                        <div class="inline-block p-2 bg-white rounded-[2rem] shadow-lg">
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="{{ $member->first_name }}"
                                     class="object-cover w-24 h-24 rounded-[1.5rem] border-2 border-emerald-50">
                            @else
                                <div class="w-24 h-24 bg-gray-50 rounded-[1.5rem] flex items-center justify-center text-3xl font-black text-emerald-600 border-2 border-emerald-50">
                                    {{ $initials }}
                                </div>
                            @endif
                        </div>

                        <h1 class="mt-4 text-2xl font-black tracking-tight text-gray-800">
                            {{ $member->first_name }} {{ $member->middle_name ? $member->middle_name . ' ' : '' }}{{ $member->last_name }} {{ $member->suffix ?? '' }}
                        </h1>
                        <p class="text-xs font-bold tracking-widest uppercase text-emerald-600/70">{{ $member->specialization ?? 'Member' }}</p>

                        @if($member->verified_at)
                            <span class="inline-flex items-center gap-1.5 mt-3 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700 border border-emerald-200">
                                <i class="fas fa-check-double text-[8px]"></i> Verified
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 mt-3 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-amber-100 text-amber-700 border border-amber-200">
                                <i class="fas fa-clock text-[8px]"></i> Pending Verification
                            </span>
                        @endif
                    </div>

                    {{-- QR Code Section --}}
                    <div class="flex flex-col items-center p-8 mt-4 border-t border-b border-gray-50 bg-gray-50/30">
                        <div class="p-4 bg-white border border-gray-100 shadow-inner rounded-3xl">
                            {!! QrCode::size(150)->margin(1)->color(31, 41, 55)->generate($member->member_id ?? 'UNASSIGNED') !!}
                        </div>
                        <div class="mt-4 text-center">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Membership No.</span>
                            <code class="px-3 py-1 font-mono text-xs font-bold text-gray-600 rounded-full bg-gray-200/50">
                                {{ $member->member_id ?? 'Unassigned' }}
                            </code>
                        </div>
                    </div>

                    {{-- Footer Info --}}
                    <div class="grid grid-cols-2 gap-4 p-8">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter block">Jurisdiction</span>
                            <p class="text-xs font-bold text-gray-700">{{ $member->province->name ?? 'N/A' }}, {{ $member->region->name ?? '' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter block">Registered Date</span>
                            <p class="text-xs font-bold text-gray-700">{{ $member->created_at ? $member->created_at->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- Decorative Watermark --}}
                    <div class="absolute bottom-0 right-0 p-4 opacity-[0.03] pointer-events-none">
                        <i class="fas fa-shield-alt text-7xl"></i>
                    </div>
                </div>

                {{-- Profile Completion: shown until everything needed for the ID is filled in --}}
                @unless($canPrint)
                    <div class="p-6 bg-white border border-amber-200 shadow-sm rounded-3xl">
                        <div class="flex items-start gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 shrink-0">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-gray-800">
                                    {{ $isSelf ? 'Complete your profile to get your ID' : 'Profile incomplete — ID not yet printable' }}
                                </h3>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    {{ $done }} of {{ $total }} required details filled in
                                </p>
                            </div>
                        </div>

                        {{-- Progress --}}
                        <div class="w-full h-2 mt-4 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-emerald-500" style="width: {{ $percent }}%"></div>
                        </div>

                        {{-- Checklist --}}
                        <ul class="mt-4 space-y-1.5">
                            @foreach(\App\Models\Member::ID_REQUIRED_FIELDS as $field => $label)
                                @php $filled = ! in_array($label, $missing, true); @endphp
                                <li class="flex items-center gap-2 text-xs font-semibold {{ $filled ? 'text-gray-400' : 'text-gray-800' }}">
                                    @if($filled)
                                        <i class="w-4 text-center fas fa-circle-check text-emerald-500"></i>
                                        <span class="line-through">{{ $label }}</span>
                                    @else
                                        <i class="w-4 text-center far fa-circle text-amber-500"></i>
                                        <span>{{ $label }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        @can('update', $member)
                            <a href="{{ $editUrl }}"
                               class="flex items-center justify-center w-full gap-2 py-3 mt-5 text-xs font-black tracking-wider text-white uppercase transition-all bg-amber-500 rounded-2xl hover:bg-amber-600 active:scale-95">
                                <i class="fas fa-pen"></i> {{ $isSelf ? 'Complete My Profile' : 'Complete Profile' }}
                            </a>
                        @endcan
                    </div>
                @endunless

                {{-- Action Buttons --}}
                <div class="flex flex-col gap-3">
                    @can('update', $member)
                        <a href="{{ $editUrl }}"
                            class="w-full py-3.5 text-sm font-black text-center text-white transition-all shadow-md bg-emerald-600 rounded-2xl hover:bg-emerald-700 active:scale-95 flex items-center justify-center gap-2">
                            <i class="fas fa-edit"></i> Edit Member Details
                        </a>
                    @endcan

                    <div class="flex gap-3">
                        @if($canPrint)
                            <a href="{{ route('members.download-id', $member) }}" target="_blank" rel="noopener"
                                class="flex-1 py-3 text-sm font-black text-white transition-all shadow-md bg-slate-900 rounded-2xl hover:bg-black active:scale-95 flex items-center justify-center gap-2">
                                <i class="fas fa-print"></i> Print ID
                            </a>
                        @else
                            <button type="button" disabled
                                title="Complete the profile to print the ID"
                                class="flex-1 py-3 text-sm font-black text-gray-400 bg-gray-100 border border-gray-200 rounded-2xl cursor-not-allowed flex items-center justify-center gap-2">
                                <i class="fas fa-lock"></i> Print ID
                            </button>
                        @endif

                        @unless($isSelf)
                            <a href="mailto:{{ $member->email }}"
                                class="flex-1 py-3 text-sm font-black text-center text-gray-700 transition-all bg-white border border-gray-200 shadow-sm rounded-2xl hover:bg-gray-50 active:scale-95 flex items-center justify-center gap-2">
                                <i class="fas fa-envelope"></i> Contact
                            </a>
                        @endunless
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Full Member Details Card --}}
            <div class="lg:col-span-7 xl:col-span-8">
                <div class="member-details-card bg-white border border-gray-100 rounded-3xl shadow-xl overflow-hidden">

                    {{-- Card Header --}}
                    <div class="flex items-center justify-between p-6 bg-gray-50/80 border-b border-gray-100">
                        <div>
                            <h3 class="text-lg font-black text-gray-800">Member Complete Information</h3>
                            <p class="text-xs text-gray-500">Comprehensive raw registration record</p>
                        </div>
                        @can('update', $member)
                            <a href="{{ $editUrl }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 hover:underline flex items-center gap-1">
                                <i class="fas fa-pencil-alt text-[10px]"></i> Edit Profile
                            </a>
                        @endcan
                    </div>

                    <div class="p-6 md:p-8 space-y-8">
                        {{-- 1. Identity & Demographics --}}
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-emerald-600 mb-4 border-b border-emerald-100 pb-2 flex items-center gap-2">
                                <i class="fas fa-user"></i> Identity & Demographics
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Full Name</span>
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ $member->first_name }} {{ $member->middle_name ? $member->middle_name . ' ' : '' }}{{ $member->last_name }} {{ $member->suffix ?? '' }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Sex</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->sex ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Civil Status</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->civil_status ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Date of Birth</span>
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ $member->dob ? \Carbon\Carbon::parse($member->dob)->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Contact Number</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->contact_no ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Email Address</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->email ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Occupation</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->occupation ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Specialization</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->specialization ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Organization / Club</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->organization->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Location & Jurisdiction --}}
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-emerald-600 mb-4 border-b border-emerald-100 pb-2 flex items-center gap-2">
                                <i class="fas fa-map-marker-alt"></i> Location & Jurisdiction
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Region</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->region->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Province</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->province->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">City / Municipality</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->city_municipality ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Barangay & Zip</span>
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ $member->barangay ?? 'N/A' }} {{ $member->zip_code ? "({$member->zip_code})" : '' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Education & RSBSA Record --}}
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-emerald-600 mb-4 border-b border-emerald-100 pb-2 flex items-center gap-2">
                                <i class="fas fa-graduation-cap"></i> Education & RSBSA Record
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Highest Education</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->highest_education ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Degree / Course</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->degree_course ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">School / Institution</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->school_name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">RSBSA Registered</span>
                                    <p class="text-sm font-bold text-gray-800">
                                        @if($member->is_rsbsa_registered)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                Yes
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                No
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">RSBSA No.</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->rsbsa_no ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">LSA Level</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->lsa_level ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- 4. Farm Profile & Assets --}}
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-emerald-600 mb-4 border-b border-emerald-100 pb-2 flex items-center gap-2">
                                <i class="fas fa-seedling"></i> Farm Profile & Assets
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Land Ownership</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->land_ownership ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Farm Area</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $member->farm_area ?? 'N/A' }}</p>
                                </div>

                                {{-- Farm Equipment Badges --}}
                                <div class="sm:col-span-2">
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block mb-2">Farm Equipment</span>
                                    @if(!empty($member->farm_equipment) && is_array($member->farm_equipment))
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($member->farm_equipment as $equipment)
                                                <span class="px-3 py-1 bg-gray-100 border border-gray-200 text-gray-700 text-xs font-semibold rounded-lg">
                                                    {{ $equipment }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm font-bold text-gray-500">None specified</p>
                                    @endif
                                </div>

                                {{-- Agricultural Skills & Certifications --}}
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Agri Skills</span>
                                    <p class="text-sm font-bold text-gray-800 whitespace-pre-line">{{ $member->agri_skills ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase block">Certifications</span>
                                    <p class="text-sm font-bold text-gray-800 whitespace-pre-line">{{ $member->certifications ?? 'N/A' }}</p>
                                </div>

                                {{-- Attachment Download --}}
                                @if($member->agri_resume_path)
                                    <div class="sm:col-span-2 pt-2">
                                        <span class="text-[11px] font-bold text-gray-400 uppercase block mb-1">Attached Agri-Resume</span>
                                        <a href="{{ route('members.download-file', $member->id) }}"
                                           class="inline-flex items-center gap-2 text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-200 hover:bg-emerald-100 transition-colors">
                                            <i class="fas fa-paperclip"></i> Download Agri-Resume
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>