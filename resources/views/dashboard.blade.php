<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-white border shadow-sm rounded-xl border-slate-100">
                    <i class="text-indigo-600 fas {{ auth()->user()->role === 'Admin' ? 'fa-shield-check' : 'fa-user-circle' }}"></i>
                </div>
                <div>
                    <h2 class="text-sm font-black tracking-tight uppercase text-slate-800">
                        {{ auth()->user()->role === 'Admin' ? 'Admin Control Center' : auth()->user()->role . ' Dashboard' }}
                    </h2>
                    <div class="flex items-center gap-2">
                        <span class="relative flex w-1.5 h-1.5">
                            <span class="absolute inline-flex w-full h-full rounded-full opacity-75 animate-ping bg-emerald-400"></span>
                            <span class="relative inline-flex w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        </span>
                        <p class="text-xs font-black tracking-widest uppercase text-slate-400">
                            @if(auth()->user()->role === 'Admin')
                                Global Oversight Active
                            @else
                                {{ auth()->user()->region?->name ?? 'Unassigned' }} Regional Scope
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- 1. Announcements & Activities --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                {{-- Announcements Section --}}
                <div class="bg-white p-6 border border-slate-100 shadow-sm rounded-[2rem]">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-black tracking-widest uppercase text-m text-slate-800">
                            {{ auth()->user()->role === 'Admin' ? 'Announcements' : 'Regional Bulletins' }}
                        </h3>
                        <span class="text-[10px] font-bold text-slate-400">Latest 5</span>
                    </div>

                    <div class="space-y-3">
                        @forelse($announcements as $post)
                            <div class="p-4 transition-all border bg-slate-50 rounded-2xl border-slate-100 group hover:bg-white hover:shadow-md">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex flex-wrap gap-1">
                                        <span class="text-[9px] font-black px-2 py-0.5 bg-indigo-100 text-indigo-600 rounded-lg uppercase tracking-widest">
                                            {{ $post->category ?? 'General' }}
                                        </span>

                                        {{-- Scope Badge --}}
                                        @if(!$post->region_id)
                                            <span class="text-[9px] font-black px-2 py-0.5 bg-purple-100 text-purple-700 rounded-lg uppercase tracking-widest">
                                                Global
                                            </span>
                                        @else
                                            <span class="text-[9px] font-black px-2 py-0.5 bg-sky-100 text-sky-700 rounded-lg uppercase tracking-widest">
                                                {{ $post->region?->name }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        @if($post->pdf_path)
                                            <a href="{{ asset('storage/' . $post->pdf_path) }}" target="_blank" class="text-red-500 hover:text-red-700" title="Download PDF">
                                                <i class="text-xs fas fa-file-pdf"></i>
                                            </a>
                                        @endif

                                        @if($post->external_link)
                                            <a href="{{ $post->external_link }}" target="_blank" class="text-blue-500 hover:text-blue-700" title="Visit Link">
                                                <i class="text-xs fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <h4 class="mt-2 text-sm font-bold leading-tight text-slate-700">{{ $post->title }}</h4>
                                <p class="mt-1 text-xs text-slate-400 line-clamp-2">{{ Str::limit($post->content, 90) }}</p>
                                <span class="block mt-2 text-[9px] font-bold text-slate-300 uppercase">
                                    Posted {{ $post->created_at->diffForHumans() }}
                                </span>
                            </div>
                        @empty
                            <div class="py-8 text-center border-2 border-dashed border-slate-100 rounded-2xl">
                                <p class="text-xs font-bold uppercase text-slate-400">No Announcements Found</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Activity Timeline --}}
                <div class="lg:col-span-2 bg-white p-6 border border-slate-100 shadow-sm rounded-[2rem]">
                    <h3 class="mb-4 font-black tracking-widest uppercase text-m text-slate-800">Activity Timeline</h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @foreach($activities as $act)
                        <div class="pl-4 border-l-2 {{ $act->is_ongoing ? 'border-emerald-400' : 'border-slate-200' }}">
                            <span class="text-xs font-black {{ $act->is_ongoing ? 'text-emerald-500' : 'text-slate-400' }} uppercase">{{ $act->date_range }}</span>
                            <h4 class="text-sm font-black text-slate-800">{{ $act->title }}</h4>
                            <p class="text-xs font-bold uppercase text-slate-400"><i class="mr-1 fas fa-location-dot"></i>{{ $act->location }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Queue Status Indicator --}}
            <div class="flex items-center gap-3 p-3 border bg-slate-50 border-slate-200 rounded-2xl">
                <div class="relative flex w-2 h-2">
                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $queue_count > 0 ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                </div>
                <div class="flex flex-col">
                    <span class="font-black tracking-widest uppercase text-xs text-slate-800">
                        Email Dispatcher: {{ $queue_count > 0 ? "Processing ({$queue_count} queued)" : 'Idle (Ready)' }}
                    </span>
                    <p class="text-[10px] font-bold uppercase text-slate-400">Auto Notifications for Regional Officers & Members</p>
                </div>
            </div>

            {{-- Regional Member Distribution Bar Chart --}}
            <div class="bg-white p-6 border border-slate-100 shadow-sm rounded-[2rem] lg:col-span-3">
                <h3 class="mb-4 font-black tracking-widest uppercase text-m text-slate-800">
                    {{ auth()->user()->role === 'Admin' ? 'Member Distribution per Region' : 'Regional Membership Scale' }}
                </h3>
                <div class="relative h-64">
                    <canvas id="regionalBarChart"></canvas>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('regionalBarChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [{
                            label: 'Total Members',
                            data: {!! json_encode($chartCounts) !!},
                            backgroundColor: '#6366f1',
                            borderRadius: 10,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { display: false },
                                ticks: { font: { weight: 'bold' } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { weight: 'bold' } }
                            }
                        }
                    }
                });
            </script>

            {{-- 3. Regional Reach & Audit --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- Regional Reach Section --}}
                <div class="bg-white p-6 border border-slate-100 shadow-sm rounded-[2rem]">
                    <h3 class="mb-4 font-black tracking-widest uppercase text-m text-slate-800">
                        {{ auth()->user()->role === 'Admin' ? 'Regional Reach' : 'Region Reach Status' }}
                    </h3>
                    <div class="space-y-4">
                        @foreach($regional_stats as $stat)
                            @php 
                                $userRegion = auth()->user()->region?->name; 
                                $isAdmin = auth()->user()->role === 'Admin';
                            @endphp

                            @if($isAdmin || $stat->name === $userRegion)
                            <div class="space-y-1">
                                <div class="flex justify-between text-xs font-black uppercase text-slate-500">
                                    <span>{{ $stat->name }}</span>
                                    <span>{{ $stat->percentage }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
                                    <div class="h-full transition-all duration-700 bg-indigo-500" style="width: {{ $stat->percentage }}%"></div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Audit Logs --}}
                <div class="bg-white p-6 border border-slate-100 shadow-sm rounded-[2rem]">
                    <h3 class="mb-4 font-black tracking-widest uppercase text-m text-slate-800">
                        {{ auth()->user()->role === 'Admin' ? 'Audit Logs' : 'Recent Activity' }}
                    </h3>
                    <div class="space-y-4">
                        @forelse($audit_logs as $log)
                            <div class="flex items-center gap-3">
                                <div class="p-1.5 bg-slate-50 rounded-lg text-slate-400">
                                    <i class="fas {{ $log->icon ?? 'fa-history' }}"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold leading-tight text-slate-700">{{ $log->activity }}</p>
                                    <p class="text-indigo-500 uppercase text-[9px] font-black">
                                        {{ $log->user?->name ?? 'System' }} • {{ $log->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="py-4 text-center">
                                <p class="text-xs font-bold uppercase text-slate-400">No recent activity recorded</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 4. Recent Members Table --}}
            <div class="bg-white border border-slate-100 shadow-sm rounded-[2rem] overflow-hidden">
                <div class="p-6 border-b border-slate-50">
                    <h3 class="font-black tracking-widest uppercase text-m text-slate-800">
                        {{ auth()->user()->role === 'Admin' ? 'All Registered Members' : 'Regional Member Roster' }}
                    </h3>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-6 py-3 font-black uppercase text-xs text-slate-400">Member</th>
                            <th class="px-6 py-3 font-black uppercase text-xs text-slate-400">Region</th>
                            <th class="px-6 py-3 font-black text-right uppercase text-xs text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recent_members as $m)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <p class="text-xs font-black text-slate-800">{{ $m->full_name }}</p>
                                <p class="text-[10px] font-bold uppercase text-slate-400">{{ $m->email }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[9px] font-black rounded border border-indigo-100 uppercase">
                                    {{ $m->region->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if(auth()->user()->role === 'Admin' || $m->region_id === auth()->user()->region_id)
                                    <button class="mr-2 text-slate-300 hover:text-indigo-600"><i class="text-xs fas fa-edit"></i></button>
                                    <button class="text-slate-300 hover:text-red-600"><i class="text-xs fas fa-trash"></i></button>
                                @else
                                    <button class="text-indigo-500 font-black text-[10px] uppercase hover:underline">View</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-xs font-bold uppercase text-slate-400">
                                No member records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>