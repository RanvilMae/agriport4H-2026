<div x-data="{ 
        open: false, 
        name: '', 
        id: '', 
        acronym: '', 
        region_id: '', 
        certification_path: '',
        action: '' 
    }" 
    @open-edit-modal.window="
        open = true; 
        id = $event.detail.id; 
        name = $event.detail.name; 
        acronym = $event.detail.acronym || ''; 
        region_id = $event.detail.region_id || ''; 
        certification_path = $event.detail.certification_path || ''; 
        action = '/organizations/' + $event.detail.id;
    "
    x-show="open" 
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto">
    
    <div class="flex items-center justify-center min-h-screen px-4">
        {{-- Backdrop --}}
        <div class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>

        {{-- Modal Content --}}
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="relative w-full max-w-lg p-8 bg-white shadow-2xl rounded-3xl my-8">
            
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black tracking-tight uppercase text-slate-800">Edit Organization</h3>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            {{-- Added enctype="multipart/form-data" for file handling --}}
            <form :action="action" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    {{-- Organization Name --}}
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-1 tracking-widest">
                            Organization Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" x-model="name" required
                               class="w-full px-4 py-3 mt-1 text-sm font-semibold border-gray-100 bg-gray-50 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-slate-800">
                    </div>

                    {{-- Acronym --}}
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-1 tracking-widest">Acronym / Short Code</label>
                        <input type="text" name="acronym" x-model="acronym"
                               class="w-full px-4 py-3 mt-1 text-sm font-semibold border-gray-100 bg-gray-50 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-slate-800">
                    </div>

                    {{-- Region Assignment --}}
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-1 tracking-widest">
                            Assigned Region <span class="text-rose-500">*</span>
                        </label>
                        @if(auth()->user()->role === 'Admin')
                            <select name="region_id" x-model="region_id" required
                                class="w-full px-4 py-3 mt-1 text-xs font-bold border-gray-100 bg-gray-50 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-slate-700 uppercase tracking-wider cursor-pointer">
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }} ({{ $region->region_code }})</option>
                                @endforeach
                            </select>
                        @else
                            <input type="hidden" name="region_id" :value="region_id">
                            <div class="px-4 py-3 mt-1 bg-slate-100 rounded-xl border border-slate-200 flex items-center justify-between">
                                <span class="text-xs font-bold uppercase text-slate-600 tracking-wider">
                                    {{ auth()->user()->region->name ?? 'Assigned Scope' }}
                                </span>
                                <i class="fas fa-lock text-slate-400 text-xs"></i>
                            </div>
                        @endif
                    </div>

                    {{-- PDF Certification File Upload --}}
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-1 tracking-widest">
                            PDF Certification Document
                        </label>

                        {{-- Active PDF Preview Badge --}}
                        <template x-if="certification_path">
                            <div class="flex items-center justify-between p-3 mt-1 mb-2 bg-emerald-50 border border-emerald-100 rounded-xl">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-file-pdf text-rose-500 text-xs"></i>
                                    <span class="text-[10px] font-black text-emerald-800 uppercase tracking-wider">Current File Attached</span>
                                </div>
                                <a :href="`/storage/${certification_path}`" 
                                   target="_blank" 
                                   class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg text-[9px] font-black uppercase tracking-wider hover:bg-emerald-700 transition-all">
                                    View PDF
                                </a>
                            </div>
                        </template>

                        <div class="p-3 mt-1 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50 hover:bg-slate-50 transition-colors">
                            <input type="file" 
                                name="certification" 
                                accept="application/pdf"
                                class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 cursor-pointer">
                            <p class="mt-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-tight">
                                <i class="fas fa-info-circle mr-0.5"></i> Selecting a file replaces the existing PDF (Max 5MB).
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end mt-8 space-x-3">
                    <button type="button" @click="open = false" 
                            class="px-5 py-2.5 text-xs font-bold text-slate-500 uppercase hover:bg-slate-50 rounded-xl transition-all">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 text-xs font-bold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all">
                        Update Organization
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>