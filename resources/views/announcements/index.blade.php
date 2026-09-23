<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black tracking-widest uppercase text-m text-slate-800">Manage Announcements</h2>
                @if(in_array(auth()->user()->role, ['President', 'Coordinator']) && auth()->user()->region)
                    <p class="text-[11px] font-bold text-slate-400 mt-0.5">
                        Region Scope: <span class="text-indigo-600">{{ auth()->user()->region->name }}</span>
                    </p>
                @endif
            </div>

            @if(in_array(auth()->user()->role, ['Admin', 'President', 'Coordinator']))
                <button onclick="openModal('addAnnouncement')" class="px-4 py-2 text-[10px] font-black text-white bg-indigo-600 rounded-lg shadow-md uppercase hover:bg-indigo-700 transition">+ New Post</button>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-4">
            
            {{-- Alerts --}}
            @if(session('success'))
                <div class="p-4 text-xs font-bold border rounded-2xl bg-emerald-50 border-emerald-100 text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-[2rem] border border-slate-100">
                <table class="w-full text-left">
                    <thead class="border-b bg-slate-50 border-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-sm font-black uppercase text-slate-400">Title</th>
                            <th class="px-6 py-4 text-sm font-black uppercase text-slate-400">Scope</th>
                            <th class="px-6 py-4 text-sm font-black uppercase text-slate-400">Category</th>
                            <th class="px-6 py-4 text-sm font-black text-center uppercase text-slate-400">Media</th>
                            <th class="px-6 py-4 text-sm font-black uppercase text-slate-400">Date</th>
                            <th class="px-6 py-4 text-sm font-black text-right uppercase text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($announcements as $ann)
                            <tr class="text-sm">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-700">{{ $ann->title }}</div>
                                    <div class="text-[10px] text-slate-400">By {{ $ann->user?->name ?? 'System' }}</div>
                                </td>

                                {{-- Scope Badge --}}
                                <td class="px-6 py-4">
                                    @if(!$ann->region_id)
                                        <span class="px-2 py-0.5 bg-purple-50 text-purple-600 rounded text-[10px] font-black uppercase tracking-tighter">Global</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-sky-50 text-sky-600 rounded text-[10px] font-black uppercase tracking-tighter">{{ $ann->region?->name }}</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded text-[10px] font-black uppercase tracking-tighter">{{ $ann->category ?? 'General' }}</span>
                                </td>
                                
                                {{-- Media Icons Logic --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-3">
                                        @if($ann->pdf_path)
                                            <a href="{{ asset('storage/' . $ann->pdf_path) }}" target="_blank" class="text-red-400 hover:text-red-600" title="View PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @else
                                            <span class="text-slate-200"><i class="fas fa-file-pdf"></i></span>
                                        @endif

                                        @if($ann->external_link)
                                            <a href="{{ $ann->external_link }}" target="_blank" class="text-blue-400 hover:text-blue-600" title="Open Link">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @else
                                            <span class="text-slate-200"><i class="fas fa-link"></i></span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-medium text-slate-400">{{ $ann->created_at->format('M d, Y') }}</td>
                                
                                {{-- Action Authorization Logic --}}
                                <td class="px-6 py-4 text-right">
                                    @if(auth()->user()->role === 'Admin' || $ann->region_id === auth()->user()->region_id)
                                        <form action="{{ route('announcements.destroy', $ann->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700">Delete</button>
                                        </form>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-300 italic">Read-Only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">
                                    No announcements found for your region.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($announcements->hasPages())
                <div class="mt-4">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal for Creating Announcements --}}
    @if(in_array(auth()->user()->role, ['Admin', 'President', 'Coordinator']))
        <div id="addAnnouncement" class="fixed inset-0 z-50 items-center justify-center hidden bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white p-8 rounded-[2rem] w-full max-w-md shadow-2xl border border-slate-100">
                <h3 class="mb-6 font-black tracking-widest uppercase text-m text-slate-800">Create Announcement</h3>
                
                <form action="{{ route('announcements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    {{-- Title --}}
                    <input type="text" name="title" required placeholder="Announcement Title" class="w-full text-xs font-bold border-slate-100 bg-slate-50 rounded-xl focus:ring-indigo-500">
                    
                    {{-- Category --}}
                    <select name="category" class="w-full text-xs font-bold border-slate-100 bg-slate-50 rounded-xl focus:ring-indigo-500">
                        <option value="Urgent">Urgent</option>
                        <option value="Update">Update</option>
                        <option value="Personnel">Personnel</option>
                        <option value="Event">Event</option>
                    </select>

                    {{-- Region Target Scoping --}}
                    <div>
                        <label class="block mb-1 text-[10px] font-black uppercase text-slate-400">Target Region Scope</label>
                        @if(auth()->user()->role === 'Admin')
                            <select name="region_id" class="w-full text-xs font-bold border-slate-100 bg-slate-50 rounded-xl focus:ring-indigo-500">
                                <option value="">-- Global (All Regions) --</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" readonly value="{{ auth()->user()->region?->name ?? 'My Region' }}" class="w-full text-xs font-bold border-slate-100 bg-slate-100 text-slate-500 rounded-xl cursor-not-allowed">
                            <input type="hidden" name="region_id" value="{{ auth()->user()->region_id }}">
                        @endif
                    </div>

                    {{-- External Link Input --}}
                    <div class="relative">
                        <i class="absolute left-3 top-3.5 fas fa-link text-slate-300 text-[10px]"></i>
                        <input type="url" name="external_link" placeholder="External Link (https://...)" class="w-full pl-8 text-xs font-bold border-slate-100 bg-slate-50 rounded-xl">
                    </div>

                    {{-- PDF File Input --}}
                    <div class="p-4 border-2 border-dashed border-slate-100 rounded-xl bg-slate-50">
                        <label class="block mb-2 text-[10px] font-black uppercase text-slate-400">Attach PDF Memo</label>
                        <input type="file" name="pdf_file" accept=".pdf" class="block w-full text-xs font-bold text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                    </div>

                    {{-- Content Details --}}
                    <textarea name="content" required placeholder="Details..." class="w-full h-24 text-xs font-bold border-slate-100 bg-slate-50 rounded-xl focus:ring-indigo-500"></textarea>

                    <div class="flex gap-2 pt-2">
                        <button type="button" onclick="closeModal('addAnnouncement')" class="flex-1 py-3 bg-slate-100 text-slate-500 rounded-xl text-[10px] font-black uppercase tracking-widest">Cancel</button>
                        <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-200">Post Now</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-app-layout>

<script>
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
</script>