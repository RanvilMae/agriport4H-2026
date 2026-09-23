<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Member Profile') }}
                </h2>
                <p class="mt-0.5 text-xs text-gray-500">View your account details, location, and agricultural background.</p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center space-x-3">
                <a href="{{ route('profile.edit') }}"
                   class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase bg-green-600 rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    {{ __('Edit Profile') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

            {{-- Success Notification --}}
            @if (session('status') === 'profile-updated')
                <div class="p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center justify-between text-green-700 text-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Profile updated successfully!</span>
                    </div>
                </div>
            @endif

            {{-- SECTION I: IDENTITY & DEMOGRAPHICS --}}
            <div class="p-6 bg-white shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        Identity & Demographics
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Full Name</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">
                            {{ $member?->first_name ?? $user->name }} {{ $member?->middle_name }} {{ $member?->last_name }} {{ $member?->suffix }}
                        </p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Date of Birth</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">
                            {{ $member?->dob ? \Carbon\Carbon::parse($member->dob)->format('M d, Y') : 'N/A' }}
                        </p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Sex</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800 capitalize">{{ $member?->sex ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Civil Status</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800 capitalize">{{ $member?->civil_status ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Contact Number</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->contact_no ?? 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-3">
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Email Address</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->email ?? $user->email }}</p>
                    </div>
                </div>
            </div>

            {{-- SECTION II: ADDRESS & LOCATION DETAILS --}}
            <div class="p-6 bg-white shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="pb-4 mb-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                        Address & Location Details
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Region</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->region?->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Province</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->province?->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">City / Municipality</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->city_municipality ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">District</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->district ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Barangay</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->barangay ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Zip Code</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->zip_code ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- SECTION III: PROFESSIONAL & PROGRAM DETAILS --}}
            <div class="p-6 bg-white shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="pb-4 mb-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 flex items-center">
                        <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                        Professional & Program Details
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Member Type</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->member_type ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Occupation</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->occupation ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Organization / Coop</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->organization_name ?? $member?->organization?->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Specialization</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->specialization ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">HVCDP Category</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->hvcdp_category ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Internship</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->internship ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Scholarship</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->scholarship ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">LSA Level & Type</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->lsa_level_type ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Training Course</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->training_course ?? 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-3">
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Crops Produced</span>
                        @if(!empty($member?->crops) && is_array($member->crops))
                            <div class="flex flex-wrap gap-2">
                                @foreach($member->crops as $crop)
                                    <span class="px-2.5 py-1 text-xs font-medium text-green-700 bg-green-50 rounded-lg border border-green-200">{{ $crop }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm font-semibold text-gray-800">N/A</p>
                        @endif
                    </div>

                    <div class="md:col-span-3">
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Services Provided</span>
                        @if(!empty($member?->services) && is_array($member->services))
                            <div class="flex flex-wrap gap-2">
                                @foreach($member->services as $service)
                                    <span class="px-2.5 py-1 text-xs font-medium text-purple-700 bg-purple-50 rounded-lg border border-purple-200">{{ $service }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm font-semibold text-gray-800">N/A</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SECTION IV: AGRI-RESUME & FIELD EXPERIENCE --}}
            <div class="p-6 bg-white shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="pb-4 mb-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 flex items-center">
                        <span class="w-2 h-2 bg-amber-500 rounded-full mr-2"></span>
                        Agri-Resume & Field Experience Details
                    </h3>
                </div>

                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">A. Educational Background</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Highest Education</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->highest_education ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Degree Course</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->degree_course ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">School / Institution</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->school_name ?? 'N/A' }}</p>
                    </div>
                </div>

                <hr class="border-gray-100 my-4">

                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">B. Farm Profile & Operational Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Land Area</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->land_area ? $member->land_area . ' Hectares' : 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Land Ownership Type</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">
                            {{ $member?->land_ownership === 'Others' ? $member?->land_ownership_others : ($member?->land_ownership ?? 'N/A') }}
                        </p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Farm Location</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->farm_location ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Farming Experience</span>
                        <p class="mt-1 text-sm font-semibold text-gray-800">{{ $member?->years_in_farming ? $member->years_in_farming . ' Years' : 'N/A' }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>