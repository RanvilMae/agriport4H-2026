<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-black tracking-tight text-gray-900 uppercase">
                    Agri-Resume Preview
                </h2>
                <p class="text-xs font-semibold text-gray-500">
                    Preview your official 4-H Club Agri-Resume before exporting or printing.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('member.agri-resume.show') }}" 
                   class="px-4 py-2 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-pen-to-square mr-1.5"></i> Edit Details
                </a>
                <a href="{{ route('member.agri-resume.download', ['format' => 'pdf']) }}" target="_blank" 
                   class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 rounded-lg shadow-sm hover:bg-emerald-700 transition-colors">
                    <i class="fa-solid fa-file-pdf mr-1.5"></i> Download PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto my-6">
        {{-- Resume Document Card --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden p-8 md:p-12">
            
            {{-- Resume Header --}}
            <div class="border-b-2 border-emerald-600 pb-6 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tight">
                        {{ $member->first_name }} {{ $member->middle_name }} {{ $member->last_name }} {{ $member->suffix }}
                    </h1>
                    <p class="text-emerald-700 font-bold uppercase tracking-wider text-sm mt-1">
                        {{ $member->specialization ?? 'Agricultural Youth Member' }}
                    </p>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 mt-2">
                        <span><i class="fa-solid fa-id-card text-emerald-600 mr-1"></i> Member ID: <strong class="font-mono text-gray-700">{{ $member->member_id ?? 'N/A' }}</strong></span>
                        @if($member->uid)
                            <span>• UID: <strong class="font-mono text-gray-700">{{ $member->uid }}</strong></span>
                        @endif
                        @if($member->verified_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                <i class="fa-solid fa-circle-check mr-1"></i> Verified
                            </span>
                        @endif
                    </div>
                </div>
                <div class="text-left md:text-right text-xs text-gray-600 space-y-1">
                    <p><i class="fa-solid fa-envelope text-gray-400 mr-1"></i> {{ $member->email }}</p>
                    <p><i class="fa-solid fa-phone text-gray-400 mr-1"></i> {{ $member->contact_no ?? 'N/A' }}</p>
                    <p><i class="fa-solid fa-location-dot text-gray-400 mr-1"></i> 
                        {{ implode(', ', array_filter([$member->barangay, $member->city_municipality, $member->district, $member->province?->name, $member->zip_code])) ?: 'N/A' }}
                    </p>
                </div>
            </div>

            {{-- Section: Bio Summary (if present) --}}
            @if($member->bio_summary)
            <div class="mb-8 bg-slate-50 p-4 rounded-xl border border-slate-100">
                <h3 class="text-xs font-black text-emerald-800 uppercase tracking-widest mb-1">
                    Professional Summary
                </h3>
                <p class="text-xs text-gray-700 leading-relaxed italic">
                    "{{ $member->bio_summary }}"
                </p>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- Left Column: Identity, Address, Education & Affiliation --}}
                <div class="space-y-6 md:col-span-1 border-r border-gray-100 pr-0 md:pr-6">
                    
                    {{-- Personal Demographics --}}
                    <div>
                        <h3 class="text-xs font-black text-emerald-800 uppercase tracking-widest border-b border-emerald-100 pb-1 mb-3">
                            Demographics
                        </h3>
                        <div class="text-xs space-y-1.5 text-gray-700">
                            <p><strong>Sex:</strong> {{ ucfirst($member->sex ?? 'N/A') }}</p>
                            <p><strong>Civil Status:</strong> {{ ucfirst($member->civil_status ?? 'N/A') }}</p>
                            <p><strong>Date of Birth:</strong> {{ $member->dob ? $member->dob->format('M d, Y') : 'N/A' }}</p>
                            <p><strong>Occupation:</strong> {{ $member->occupation ?? 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- Affiliation & Registration --}}
                    <div>
                        <h3 class="text-xs font-black text-emerald-800 uppercase tracking-widest border-b border-emerald-100 pb-1 mb-3">
                            Affiliation & RSBSA
                        </h3>
                        <div class="text-xs space-y-1.5 text-gray-700">
                            <p><strong>Organization:</strong> {{ $member->organization?->name ?? 'N/A' }}</p>
                            <p><strong>Region:</strong> {{ $member->region?->name ?? 'N/A' }}</p>
                            <p><strong>Member Type:</strong> {{ $member->member_type ?? 'N/A' }}</p>
                            <p>
                                <strong>RSBSA Registered:</strong> 
                                @if($member->is_rsbsa_registered)
                                    <span class="text-emerald-600 font-bold">Yes</span> ({{ $member->rsbsa_no }})
                                @else
                                    <span class="text-gray-400">No</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Education --}}
                    <div>
                        <h3 class="text-xs font-black text-emerald-800 uppercase tracking-widest border-b border-emerald-100 pb-1 mb-3">
                            Education
                        </h3>
                        <div class="text-xs space-y-1 text-gray-700">
                            <p class="font-bold text-slate-800">{{ $member->highest_education ?? 'N/A' }}</p>
                            <p class="text-gray-600">{{ $member->degree_course }}</p>
                            <p class="text-gray-400 italic">{{ $member->school_name }}</p>
                        </div>
                    </div>

                    {{-- Farm Equipment --}}
                    @if(!empty($member->farm_equipment))
                    <div>
                        <h3 class="text-xs font-black text-emerald-800 uppercase tracking-widest border-b border-emerald-100 pb-1 mb-3">
                            Farm Equipment
                        </h3>
                        <div class="flex flex-wrap gap-1">
                            @foreach((array)$member->farm_equipment as $equipment)
                                <span class="px-2 py-0.5 bg-slate-100 border border-slate-200 text-slate-700 rounded text-[10px] font-medium">
                                    {{ $equipment }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>

                {{-- Right Column: Program Details, Farm Profile & Agri Skills --}}
                <div class="space-y-6 md:col-span-2">
                    
                    {{-- Farm Profile & Land Details --}}
                    <div>
                        <h3 class="text-xs font-black text-emerald-800 uppercase tracking-widest border-b border-emerald-100 pb-1 mb-3">
                            Farm Profile
                        </h3>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">Land Ownership</span>
                                <span class="font-bold text-slate-800">{{ $member->land_ownership ?? 'N/A' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">Farm Area</span>
                                <span class="font-bold text-slate-800">{{ $member->farm_area ? $member->farm_area . ' Hectares' : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Crops & Agricultural Services --}}
                    <div>
                        <h3 class="text-xs font-black text-emerald-800 uppercase tracking-widest border-b border-emerald-100 pb-1 mb-3">
                            Crops & Services Offered
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-gray-500 font-bold block mb-1">Crops Cultivated:</span>
                                @if(!empty($member->crops))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach((array)$member->crops as $crop)
                                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded text-[10px] font-semibold">
                                                {{ $crop }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-400 italic">None specified</p>
                                @endif
                            </div>

                            <div>
                                <span class="text-gray-500 font-bold block mb-1">Services Offered:</span>
                                @if(!empty($member->services))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach((array)$member->services as $service)
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 rounded text-[10px] font-semibold">
                                                {{ $service }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-400 italic">None specified</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Program & Development Details --}}
                    <div>
                        <h3 class="text-xs font-black text-emerald-800 uppercase tracking-widest border-b border-emerald-100 pb-1 mb-3">
                            Programs & Classifications
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-xs text-gray-700">
                            <p><strong>HVCDP Category:</strong> {{ $member->hvcdp_category ?? 'N/A' }}</p>
                            <p><strong>LSA Level:</strong> {{ $member->lsa_level ?? 'N/A' }}</p>
                            <p><strong>LSA Type:</strong> {{ $member->lsa_type ?? 'N/A' }}</p>
                            <p><strong>Internship:</strong> {{ $member->internship ?? 'N/A' }}</p>
                            <p><strong>Scholarship:</strong> {{ $member->scholarship ?? 'N/A' }}</p>
                            <p><strong>Training Course:</strong> {{ $member->training_course ?? 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- Agricultural Skills --}}
                    <div>
                        <h3 class="text-xs font-black text-emerald-800 uppercase tracking-widest border-b border-emerald-100 pb-1 mb-3">
                            Agricultural Skills & Specializations
                        </h3>
                        <p class="text-xs text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $member->agri_skills ?? 'No specific skills defined.' }}
                        </p>
                    </div>

                    {{-- Certifications --}}
                    <div>
                        <h3 class="text-xs font-black text-emerald-800 uppercase tracking-widest border-b border-emerald-100 pb-1 mb-3">
                            Certifications & Trainings
                        </h3>
                        <p class="text-xs text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $member->certifications ?? 'No certifications added.' }}
                        </p>
                    </div>

                </div>

            </div>

            {{-- Document Footer --}}
            <div class="mt-12 pt-4 border-t border-gray-100 flex justify-between items-center text-[10px] text-gray-400 uppercase tracking-widest">
                <span>Official Document • 4-H Club Philippines</span>
                <span>Generated: {{ now()->format('M d, Y') }}</span>
            </div>

        </div>
    </div>
</x-app-layout>