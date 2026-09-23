<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-white shadow-sm rounded-xl">
                <i class="fas fa-sitemap text-emerald-600"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-800">Organization Directory</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Register Regional Entities</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        
        {{-- Success Notification --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="flex items-center p-4 mb-6 transition-all border shadow-sm bg-emerald-50 border-emerald-100 rounded-2xl">
                <div class="flex items-center justify-center w-8 h-8 mr-3 text-white rounded-full bg-emerald-500">
                    <i class="text-xs fas fa-check"></i>
                </div>
                <p class="text-xs font-black tracking-widest uppercase text-emerald-800">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Error/Validation Notification --}}
        @if($errors->any())
            <div class="p-4 mb-6 border shadow-sm bg-rose-50 border-rose-100 rounded-2xl">
                <div class="flex items-center mb-2 space-x-2">
                    <i class="text-xs fas fa-exclamation-triangle text-rose-500"></i>
                    <h4 class="text-[10px] font-black text-rose-700 uppercase tracking-widest">Entry Error</h4>
                </div>
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-xs font-bold text-rose-600">— {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Main Form Card --}}
        <div class="p-10 bg-white border border-gray-100 shadow-2xl shadow-slate-200/60 rounded-[2.5rem]">
            <div class="flex items-center mb-10 space-x-4">
                <div class="flex items-center justify-center w-10 h-10 text-white shadow-lg rounded-2xl bg-slate-900 shadow-slate-200">
                    <i class="text-xs fas fa-plus"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black tracking-[0.15em] uppercase text-slate-700">New Organization</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ensure unique regional assignment</p>
                </div>
            </div>

            <form action="{{ route('organizations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    {{-- Region Selection --}}
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Regional Jurisdiction</label>
                        
                        @if(auth()->user()->hasRole('Admin'))
                            {{-- Admin can select any region --}}
                            <select name="region_id" required 
                                    class="w-full px-5 py-4 mt-2 text-xs font-bold border-gray-100 bg-gray-50/50 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 transition-all @error('region_id') border-rose-200 bg-rose-50/30 @enderror">
                                <option value="" disabled selected>Select assigned region...</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }} ({{ $region->region_code }})
                                    </option>
                                @endforeach
                            </select>
                        @else
                            {{-- Non-Admin: Forces region_id to match user's assigned region_id --}}
                            <input type="hidden" name="region_id" value="{{ auth()->user()->region_id }}">
                            
                            <div class="px-5 py-4 mt-2 bg-slate-50/80 rounded-2xl border border-slate-200 flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-map-marker-alt text-emerald-600 text-xs"></i>
                                    <span class="text-xs font-bold text-slate-700">
                                        {{ auth()->user()->region->name ?? 'Assigned Scope' }} 
                                        <span class="text-emerald-600 font-extrabold">({{ auth()->user()->region->region_code ?? 'N/A' }})</span>
                                    </span>
                                </div>
                                <span class="px-2.5 py-1 text-[9px] font-black tracking-widest text-slate-400 uppercase bg-white border border-slate-200 rounded-lg">
                                    <i class="fas fa-lock mr-1 text-[8px]"></i> Locked
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Org Name --}}
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Full Organization Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Siglat 4-H Club" required
                               class="w-full px-5 py-4 mt-2 text-xs font-bold border-gray-100 bg-gray-50/50 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 transition-all @error('name') border-rose-200 bg-rose-50/30 @enderror">
                    </div>

                    {{-- Acronym & Category --}}
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Acronym</label>
                            <input type="text" name="acronym" value="{{ old('acronym') }}" placeholder="S4HC"
                                   class="w-full px-5 py-4 mt-2 text-xs font-bold transition-all border-gray-100 bg-gray-50/50 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Category</label>
                            <select name="category" required 
                                    class="w-full px-5 py-4 mt-2 text-xs font-bold transition-all border-gray-100 bg-gray-50/50 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="LGU" {{ old('category') == 'LGU' ? 'selected' : '' }}>LGU</option>
                                <option value="PO" {{ old('category') == 'PO' ? 'selected' : '' }}>PO</option>
                                <option value="NGO" {{ old('category') == 'NGO' ? 'selected' : '' }}>NGO</option>
                                <option value="Academe" {{ old('category') == 'Academe' ? 'selected' : '' }}>Academe</option>
                            </select>
                        </div>
                    </div>

                    {{-- PDF Certification Upload Field --}}
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">PDF Certification Document</label>
                        <div class="p-4 mt-2 border-2 border-dashed border-slate-200 rounded-2xl bg-gray-50/50 hover:bg-gray-50 transition-colors">
                            <input type="file" 
                                name="certification" 
                                id="certification" 
                                accept="application/pdf"
                                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-wider file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 cursor-pointer">
                            <p class="mt-2 text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                <i class="fas fa-info-circle mr-1"></i> Accepted format: PDF only (Max file size: 5MB)
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full py-5 font-black text-[10px] text-white uppercase tracking-[0.3em] bg-emerald-600 rounded-3xl hover:bg-emerald-700 shadow-2xl shadow-emerald-100 transition-all transform active:scale-[0.98]">
                        Save to Directory
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>