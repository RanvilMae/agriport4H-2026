<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-white border shadow-sm rounded-xl border-emerald-50">
                    <i class="fas fa-sitemap text-emerald-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black leading-tight text-gray-800">Organization Directory</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Regional Registry Management</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div x-data="{ pdfUrl: null, pdfTitle: '', showPdfModal: false }" class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Success & Error Notifications --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="flex items-center p-4 mb-6 transition-all border shadow-sm bg-emerald-50 border-emerald-100 rounded-2xl">
                <i class="mr-3 text-sm fas fa-check-circle text-emerald-500"></i>
                <p class="text-[10px] font-black text-emerald-800 uppercase tracking-[0.2em]">{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-6 border shadow-sm bg-rose-50 border-rose-100 rounded-2xl">
                <div class="flex items-center mb-2 space-x-2 text-rose-600">
                    <i class="text-xs fas fa-exclamation-triangle"></i>
                    <h4 class="text-[10px] font-black uppercase tracking-widest">Action Denied</h4>
                </div>
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-[10px] font-bold text-rose-500 uppercase tracking-tight">• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Search & Filter Toolbar --}}
        <form action="{{ route('organizations.index') }}" method="GET"
            class="flex flex-col items-center justify-between gap-4 p-5 mb-6 bg-white border border-gray-100 shadow-sm md:flex-row rounded-3xl">
            
            <div class="flex flex-1 w-full gap-3">
                {{-- Live Search Input --}}
                <div class="relative flex-grow max-w-md">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <i class="text-[10px] fas fa-search text-slate-400"></i>
                    </div>
                    <input type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search organization or acronym..."
                        class="w-full py-3 pl-10 pr-4 text-xs font-bold transition-all border-none bg-slate-50 rounded-2xl focus:ring-2 focus:ring-emerald-500 placeholder:text-slate-400">
                </div>

                {{-- Region Filter --}}
                <select name="region" onchange="this.form.submit()"
                    class="px-4 py-3 text-[10px] font-black border-none bg-slate-50 rounded-2xl focus:ring-2 focus:ring-emerald-500 cursor-pointer uppercase tracking-widest text-slate-600">
                    <option value="">All Regions</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ request('region') == $region->id ? 'selected' : '' }}>
                            {{ $region->region_code ?? $region->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="px-5 py-3 text-[10px] font-black text-white uppercase bg-emerald-600 rounded-2xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100">
                    Search
                </button>
            </div>

            <a href="{{ route('organizations.create') }}"
                class="w-full md:w-auto px-6 py-3.5 bg-slate-900 hover:bg-emerald-600 text-white text-[10px] font-black rounded-2xl transition-all shadow-xl shadow-slate-100 flex items-center justify-center uppercase tracking-[0.2em]">
                <i class="mr-2 fas fa-plus text-[8px]"></i> New Organization
            </a>
        </form>

        {{-- Table Container --}}
        <div class="bg-white border border-gray-100 shadow-sm rounded-[2.5rem] overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b bg-slate-50/50 border-gray-50">
                        <th class="px-8 py-5 text-xs font-black tracking-widest uppercase text-slate-500">Region</th>
                        <th class="px-4 py-5 text-xs font-black tracking-widest uppercase text-slate-500">Organization Name</th>
                        <th class="px-4 py-5 text-xs font-black tracking-widest uppercase text-slate-500">Status</th>
                        <th class="px-4 py-5 text-xs font-black tracking-widest uppercase text-slate-500">Certification PDF</th>
                        <th class="px-8 py-5 text-xs font-black tracking-widest text-right uppercase text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($organizations as $org)
                    <tr class="transition-colors hover:bg-slate-50/80 group">
                        {{-- Region Badge --}}
                        <td class="px-8 py-4">
                            <span class="px-3 py-1 text-xs font-bold transition-all bg-white border rounded-lg shadow-sm border-slate-200 text-slate-600 group-hover:border-emerald-200">
                                {{ $org->region->region_code ?? 'N/A' }}
                            </span>
                        </td>

                        {{-- Name & Acronym --}}
                        <td class="px-4 py-5">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-800">{{ $org->name }}</span>
                                @if($org->acronym)
                                    <span class="text-xs font-medium uppercase text-emerald-600">{{ $org->acronym }}</span>
                                @endif
                            </div>
                        </td>

                        {{-- Verification Status Badge --}}
                        <td class="px-4 py-5">
                            @if($org->is_verified)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 uppercase tracking-wider">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            @endif
                        </td>

                        {{-- Certification File Badge --}}
                        <td class="px-4 py-5">
                            @if($org->certification_path)
                                <button type="button"
                                    @click="pdfUrl = '{{ asset('storage/' . $org->certification_path) }}'; pdfTitle = '{{ addslashes($org->name) }} Certification'; showPdfModal = true;"
                                    class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl text-[10px] font-black uppercase hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-file-pdf text-rose-500 group-hover:text-white"></i>
                                    <span>Preview PDF</span>
                                </button>
                            @else
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                    No File
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-8 py-5 text-right space-x-2">
                            {{-- Verify / Unverify Button --}}
                            <form action="{{ route('organizations.verify', $org->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                @if(!$org->is_verified)
                                    <button type="submit" 
                                        class="px-3 py-1.5 bg-emerald-600 text-white hover:bg-emerald-700 text-[10px] font-black uppercase rounded-xl transition-all shadow-sm">
                                        <i class="fas fa-check-circle mr-1"></i> Verify
                                    </button>
                                @else
                                    <button type="submit" 
                                        class="px-3 py-1.5 bg-amber-500 text-white hover:bg-amber-600 text-[10px] font-black uppercase rounded-xl transition-all shadow-sm">
                                        <i class="fas fa-undo mr-1"></i> Unverify
                                    </button>
                                @endif
                            </form>

                            {{-- Edit Button --}}
                            <button @click="$dispatch('open-edit-modal', { 
                                        id: {{ $org->id }}, 
                                        name: '{{ addslashes($org->name) }}',
                                        acronym: '{{ addslashes($org->acronym ?? '') }}',
                                        region_id: {{ $org->region_id }},
                                        certification_path: '{{ $org->certification_path ?? '' }}'
                                    })" 
                                    class="px-3 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 text-[10px] font-black uppercase rounded-xl transition-all">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-slate-400">
                            <i class="mb-3 text-2xl fas fa-search opacity-20"></i>
                            <p class="text-xs font-bold tracking-widest uppercase">No organizations found matching your filters.</p>
                            @if(request()->anyFilled(['search', 'region']))
                                <a href="{{ route('organizations.index') }}" class="text-emerald-600 underline text-[10px] font-black mt-2 inline-block">CLEAR ALL FILTERS</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if(method_exists($organizations, 'links'))
                <div class="px-8 py-5 border-t bg-slate-50/50 border-gray-50">
                    {{ $organizations->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- PDF Preview Modal --}}
        <div x-show="showPdfModal" 
             x-cloak
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            
            <div @click.away="showPdfModal = false" 
                 class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 w-full max-w-4xl h-[85vh] flex flex-col overflow-hidden">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-pdf text-rose-500 text-lg"></i>
                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-700" x-text="pdfTitle">Document Preview</h3>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a :href="pdfUrl" target="_blank" class="px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase hover:bg-emerald-700 transition-all">
                            <i class="fas fa-external-link-alt mr-1"></i> Open in New Tab
                        </a>
                        <button @click="showPdfModal = false" class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="flex-grow bg-slate-100 relative">
                    <template x-if="pdfUrl">
                        <iframe :src="pdfUrl" class="w-full h-full border-0"></iframe>
                    </template>
                </div>
            </div>
        </div>

    </div>

    @include('organizations.partials.edit-modal')

</x-app-layout>