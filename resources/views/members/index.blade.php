<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col">
                <h2 class="text-2xl font-black leading-tight text-slate-800">Member Registry</h2>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs font-bold tracking-widest uppercase text-slate-400">
                        {{ auth()->user()->role === 'Admin' ? 'Classified by Regional Jurisdiction' : 'Regional Member Directory' }}
                    </p>

                    @if($regions->count() === 1)
                        <span class="flex items-center gap-1.5 bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-tighter border border-emerald-200">
                            <i class="fas fa-lock text-[8px]" aria-hidden="true"></i>
                            {{ $regions->first()->name }} Scope
                        </span>
                    @endif
                </div>
            </div>

            @if(in_array(auth()->user()->role, ['Admin', 'President', 'Coordinator']))
                <a href="{{ route('members.create') }}"
                   class="inline-flex items-center justify-center gap-2 bg-emerald-600 text-white px-6 py-3 rounded-2xl text-sm font-black shadow-xl shadow-emerald-100 hover:bg-emerald-700 hover:-translate-y-0.5 transition-all active:scale-95">
                    <i class="fas fa-user-plus text-xs" aria-hidden="true"></i>
                    <span>Register Member</span>
                </a>
            @endif
        </div>
    </x-slot>

    <div x-data="{ resumeUrl: null, resumeTitle: '', showResumeModal: false, search: '', statusFilter: '' }" 
         @keydown.escape.window="showResumeModal = false"
         class="px-4 py-8 mx-auto max-w-7xl">

        {{-- Search and Filter Toolbar --}}
        <div class="p-4 mb-8 bg-white border shadow-sm rounded-3xl border-slate-100">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="relative">
                    <i class="absolute text-xs -translate-y-1/2 fas fa-search left-4 top-1/2 text-slate-400" aria-hidden="true"></i>
                    <input x-model="search" type="text" placeholder="Search members by name or email..." 
                           class="w-full pl-10 pr-4 py-2.5 text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none text-slate-700 placeholder-slate-400">
                </div>

                <div class="relative">
                    <i class="absolute text-xs -translate-y-1/2 fas fa-filter left-4 top-1/2 text-slate-400" aria-hidden="true"></i>
                    <select x-model="statusFilter" class="w-full pl-10 pr-4 py-2.5 text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none text-slate-700 appearance-none">
                        <option value="">All Verification Statuses</option>
                        <option value="verified">Verified Only</option>
                        <option value="pending">Pending Only</option>
                    </select>
                </div>

                <div class="flex items-center justify-end px-2 text-xs font-bold text-slate-400">
                    <span>Total Direct Entries: {{ $members->count() }}</span>
                </div>
            </div>
        </div>

        {{-- Hidden Form for CSRF-protected Member Verification --}}
        <form id="verify-member-form" action="" method="POST" class="hidden">
            @csrf
            @method('PATCH')
        </form>

        @forelse($members->groupBy('region.name') as $regionName => $regionMembers)
            <div class="mb-12">
                <div class="flex items-center gap-4 mb-4 ml-4">
                    <div class="flex-1 h-px bg-slate-200/60"></div>
                    <div class="flex flex-col items-center">
                        <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] bg-emerald-50 px-4 py-1.5 rounded-full border border-emerald-100 shadow-sm">
                            {{ $regionName ?: 'Unassigned Region' }}
                        </h3>
                        <span class="mt-1 text-[9px] font-bold tracking-widest uppercase text-slate-400">
                            {{ $regionMembers->count() }} Registered {{ Str::plural('Member', $regionMembers->count()) }}
                        </span>
                    </div>
                    <div class="flex-1 h-px bg-slate-200/60"></div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="overflow-x-auto overflow-y-visible">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b bg-slate-50/60 border-slate-100">
                                    <th scope="col" class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Full Name & Email</th>
                                    <th scope="col" class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Organization</th>
                                    <th scope="col" class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Specialization</th>
                                    <th scope="col" class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Agri Resume File</th>
                                    <th scope="col" class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Verification</th>
                                    <th scope="col" class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">LSA Status</th>
                                    <th scope="col" class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($regionMembers as $member)
                                    <tr x-show="(search === '' || '{{ strtolower($member->first_name . ' ' . $member->last_name . ' ' . $member->email) }}'.includes(search.toLowerCase())) && 
                                                (statusFilter === '' || (statusFilter === 'verified' && '{{ $member->verified_at ? '1' : '0' }}' === '1') || (statusFilter === 'pending' && '{{ $member->verified_at ? '1' : '0' }}' === '0'))"
                                        class="transition-colors hover:bg-slate-50/80 group">
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black tracking-tight text-slate-800">
                                                    {{ $member->last_name }}, {{ $member->first_name }}
                                                </span>
                                                <span class="text-[10px] font-mono text-emerald-600 font-bold uppercase tracking-tighter">{{ $member->email }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs font-bold text-slate-600">{{ $member->organization->name ?? 'Independent' }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-[10px] font-black text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg uppercase tracking-wider">
                                                {{ $member->specialization ?? 'N/A' }}
                                            </span>
                                        </td>

                                        {{-- Agri Resume Preview File Column --}}
                                        <td class="px-6 py-4 text-center">
                                            @if($member->agri_resume_path)
                                                <button type="button"
                                                    @click="resumeUrl = '{{ asset('storage/' . $member->agri_resume_path) }}'; resumeTitle = '{{ e($member->first_name . ' ' . $member->last_name) }} - Agri Resume'; showResumeModal = true;"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200/60 hover:bg-emerald-600 hover:text-white transition-all shadow-sm group/btn">
                                                    <i class="text-rose-500 fas fa-file-pdf group-hover/btn:text-white" aria-hidden="true"></i>
                                                    <span>View Attachment</span>
                                                </button>
                                            @else
                                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">None</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            @if($member->verified_at)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700 border border-emerald-200">
                                                    <i class="fas fa-check-double text-[8px]" aria-hidden="true"></i> Verified
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-amber-100 text-amber-700 border border-amber-200">
                                                    <i class="fas fa-clock text-[8px]" aria-hidden="true"></i> Pending
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $member->lsa_level === 'Platinum' ? 'bg-slate-900 text-emerald-400' : 'bg-emerald-100 text-emerald-700' }}">
                                                {{ $member->lsa_level ?? 'Standard' }}
                                            </span>
                                        </td>

                                        {{-- Actions Column --}}
                                        <td class="px-6 py-4 text-right">
                                            <div x-data="{ menuOpen: false }" class="relative inline-block text-left">
                                                <button @click="menuOpen = !menuOpen" 
                                                        @click.away="menuOpen = false"
                                                        type="button"
                                                        class="inline-flex items-center justify-center p-2 transition-all text-slate-400 hover:text-slate-600 hover:bg-slate-100 focus:outline-none rounded-xl w-8 h-8"
                                                        :aria-expanded="menuOpen.toString()" 
                                                        aria-haspopup="true"
                                                        aria-label="Actions Menu">
                                                    <i class="text-xs fas fa-ellipsis-v" aria-hidden="true"></i>
                                                </button>

                                                <div x-show="menuOpen" 
                                                     x-cloak
                                                     x-transition:enter="transition ease-out duration-100"
                                                     x-transition:enter-start="transform opacity-0 scale-95"
                                                     x-transition:enter-end="transform opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-75"
                                                     x-transition:leave-start="transform opacity-100 scale-100"
                                                     x-transition:leave-end="transform opacity-0 scale-95"
                                                     class="absolute right-0 z-50 w-48 mt-2 origin-top-right bg-white border divide-y shadow-2xl rounded-2xl border-slate-100 divide-slate-100 focus:outline-none">
                                                    
                                                    <div class="py-1">
                                                        @if(in_array(auth()->user()->role, ['Admin', 'President', 'Coordinator']) && !$member->verified_at)
                                                            <button type="button"
                                                                    data-member-name="{{ $member->first_name }} {{ $member->last_name }}"
                                                                    data-member-region="{{ $member->region_id }}"
                                                                    data-user-region="{{ auth()->user()->region_id }}"
                                                                    data-user-role="{{ auth()->user()->role }}"
                                                                    data-verify-url="{{ route('members.verify', $member) }}"
                                                                    onclick="handleVerificationClick(this)"
                                                                    class="flex items-center w-full px-4 py-2.5 text-xs font-bold text-emerald-600 hover:bg-emerald-50 transition-colors text-left">
                                                                <i class="mr-2.5 fas fa-shield-alt text-emerald-500" aria-hidden="true"></i> 
                                                                <span>Verify Member</span>
                                                            </button>
                                                        @endif

                                                        <a href="{{ route('members.agri-resume', $member) }}" 
                                                           class="flex items-center px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-emerald-600 transition-colors">
                                                            <i class="mr-2.5 fas fa-file-invoice text-slate-400" aria-hidden="true"></i> 
                                                            <span>View Agri-Resume</span>
                                                        </a>

                                                        <a href="{{ route('members.show', $member) }}" 
                                                           class="flex items-center px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-emerald-600 transition-colors">
                                                            <i class="mr-2.5 fas fa-id-card text-slate-400" aria-hidden="true"></i> 
                                                            <span>View Digital ID</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-16 bg-white rounded-[2.5rem] border border-slate-100 text-center">
                <div class="flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-slate-50 text-slate-400">
                    <i class="text-2xl fas fa-users-slash" aria-hidden="true"></i>
                </div>
                <h4 class="text-base font-black text-slate-700">No Members Found</h4>
                <p class="mt-1 text-xs font-bold text-slate-400">There are no registered members available in your current view scope.</p>
            </div>
        @endforelse

        {{-- Pagination Links (if available) --}}
        @if(method_exists($members, 'links'))
            <div class="mt-8">
                {{ $members->links() }}
            </div>
        @endif

        {{-- Agri Resume Preview Modal --}}
        <div x-show="showResumeModal" 
             x-cloak
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-slate-900/60 backdrop-blur-sm">

            <div @click.away="showResumeModal = false" 
                 class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 w-full max-w-4xl h-[85vh] flex flex-col overflow-hidden">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <div class="flex items-center space-x-3">
                        <i class="text-lg fas fa-file-pdf text-rose-500" aria-hidden="true"></i>
                        <h3 class="text-xs font-black tracking-widest uppercase text-slate-700" x-text="resumeTitle">Agri Resume Preview</h3>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a :href="resumeUrl" target="_blank" class="px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase hover:bg-emerald-700 transition-all">
                            <i class="mr-1 fas fa-external-link-alt" aria-hidden="true"></i> Open in New Tab
                        </a>
                        <button @click="showResumeModal = false" class="p-2 transition-colors text-slate-400 hover:text-slate-600" aria-label="Close Modal">
                            <i class="text-sm fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="relative flex-grow bg-slate-100">
                    <template x-if="resumeUrl">
                        <iframe :src="resumeUrl" class="w-full h-full border-0"></iframe>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function handleVerificationClick(button) {
            const name = button.dataset.memberName;
            const memberRegion = button.dataset.memberRegion;
            const userRegion = button.dataset.userRegion;
            const userRole = button.dataset.userRole;
            const verifyUrl = button.dataset.verifyUrl;

            confirmVerification(name, memberRegion, userRegion, userRole, verifyUrl);
        }

        function confirmVerification(name, memberRegion, userRegion, userRole, verifyUrl) {
            if (userRole !== 'Admin' && String(memberRegion) !== String(userRegion)) {
                Swal.fire({
                    title: 'Access Denied',
                    text: 'You are only authorized to verify members within your assigned region.',
                    icon: 'error',
                    confirmButtonColor: '#10b981',
                    customClass: { 
                        popup: 'rounded-[2.5rem]', 
                        confirmButton: 'rounded-xl px-6 py-3 font-black text-sm uppercase' 
                    }
                });
                return;
            }

            Swal.fire({
                title: 'Verify Membership?',
                text: `Confirming active status for ${name}.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Verify',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                customClass: {
                    popup: 'rounded-[2.5rem]',
                    confirmButton: 'rounded-xl px-6 py-3 font-black text-sm uppercase',
                    cancelButton: 'rounded-xl px-6 py-3 font-black text-sm uppercase'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('verify-member-form');
                    form.action = verifyUrl;
                    form.submit();
                }
            });
        }
    </script>
    @endpush
</x-app-layout>