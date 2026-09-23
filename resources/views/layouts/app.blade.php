<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', '4-H LSA') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Charting & Icons --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full overflow-hidden font-sans antialiased text-gray-900 bg-gray-50" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden bg-gray-100">

        {{-- Mobile Sidebar Overlay --}}
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm md:hidden" 
             @click="sidebarOpen = false"
             style="display: none;"></div>

        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 transition-transform duration-300 transform bg-white border-r border-emerald-100 shadow-2xl md:translate-x-0 md:static md:inset-0 md:shadow-none">
    
            {{-- Brand Header --}}
            <div class="px-5 py-6 border-b border-gray-50 bg-emerald-50/30">
                <div class="flex flex-col items-center">
                    <div class="p-2 mb-2.5 transition-transform transform shadow-lg shadow-emerald-200/50 hover:rotate-6">
                        <img src="{{ asset('images/logo.png') }}" class="object-contain w-16 h-16" alt="4-H Official Logo">
                    </div>
                    <h2 class="text-base font-black leading-tight tracking-tight text-center text-slate-800">
                        4-H CLUB <span class="uppercase text-emerald-600">Philippines</span>
                    </h2>
                    <div class="flex items-center mt-1.5 space-x-1.5">
                        <span class="w-1 h-1 rounded-full bg-emerald-400"></span>
                        <p class="text-[9px] font-bold text-emerald-700 uppercase tracking-[0.18em]">Information System</p>
                        <span class="w-1 h-1 rounded-full bg-emerald-400"></span>
                    </div>
                </div>
            </div>

            {{-- Navigation Items --}}
            <nav class="flex-1 px-3 mt-4 space-y-1 overflow-y-auto custom-scrollbar" aria-label="Sidebar Navigation">
                
                {{-- Global Dashboard Link --}}
                <a href="{{ route('dashboard') }}"
                   class="group flex items-center space-x-2.5 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'text-slate-500 hover:bg-emerald-50 hover:text-emerald-700' }}">
                    <i class="w-4 text-sm text-center fas fa-chart-pie {{ request()->routeIs('dashboard') ? 'text-white' : 'group-hover:text-emerald-600' }}" aria-hidden="true"></i>
                    <span class="text-xs font-bold">Dashboard</span>
                </a>

                {{-- MEMBER / PERSONAL ACCOUNT NAVIGATION (Visible to Members and Presidents) --}}
                @if(in_array(auth()->user()?->role, ['Member', 'President']))
                    <div class="px-3 pt-3 pb-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">My Account</p>
                    </div>

                    {{-- Member Profile Details -> members/show.blade.php --}}
                    <a href="{{ route('member.profile.show') }}" 
                       class="group flex items-center space-x-2.5 px-3 py-2 rounded-lg transition-all {{ request()->routeIs('member.profile.show') ? 'bg-emerald-50 text-emerald-700 border-l-2 border-emerald-600 font-bold' : 'text-slate-500 hover:bg-gray-50' }}">
                        <i class="w-4 text-xs text-center fas fa-user-circle" aria-hidden="true"></i>
                        <span class="text-xs">My Profile & Details</span>
                    </a>

                    {{-- Agri-Resume Preview -> members/showAgri-resume-preview.blade.php --}}
                    <a href="{{ route('members.agri-resume.preview') }}" 
                       class="group flex items-center space-x-2.5 px-3 py-2 rounded-lg transition-all {{ request()->routeIs('member.agri-resume.preview') ? 'bg-emerald-50 text-emerald-700 border-l-2 border-emerald-600 font-bold' : 'text-slate-500 hover:bg-gray-50' }}">
                        <i class="w-4 text-xs text-center fas fa-file-invoice text-emerald-600" aria-hidden="true"></i>
                        <span class="text-xs">Agri-Resume Preview</span>
                    </a>

                    {{-- Member Announcements View --}}
                    <a href="{{ route('announcements.index') }}" 
                       class="group flex items-center space-x-2.5 px-3 py-2 rounded-lg transition-all {{ request()->routeIs('announcements.*') ? 'bg-emerald-50 text-emerald-700 border-l-2 border-emerald-600 font-bold' : 'text-slate-500 hover:bg-gray-50' }}">
                        <i class="w-4 text-xs text-center text-indigo-500 fas fa-bullhorn" aria-hidden="true"></i>
                        <span class="text-xs">Announcements</span>
                    </a>
                @endif

                {{-- ADMIN / COORDINATOR / PRESIDENT NAVIGATION --}}
                @if(in_array(auth()->user()?->role, ['Admin', 'President', 'Coordinator']))
                    <div class="px-3 pt-4 pb-1 mt-3 border-t border-gray-100">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Registry Management</p>
                    </div>

                    <a href="{{ route('members.index') }}" 
                       class="group flex items-center space-x-2.5 px-3 py-2 rounded-lg transition-all {{ request()->routeIs('members.index') || request()->routeIs('members.edit') || request()->routeIs('members.create') ? 'bg-emerald-50 text-emerald-700 border-l-2 border-emerald-600 font-bold' : 'text-slate-500 hover:bg-gray-50' }}">
                        <i class="w-4 text-xs text-center fas fa-address-book" aria-hidden="true"></i>
                        <span class="text-xs">4-H Members Directory</span>
                    </a>

                    <a href="{{ route('organizations.index') }}" 
                       class="group flex items-center space-x-2.5 px-3 py-2 rounded-lg transition-all {{ request()->routeIs('organizations.*') ? 'bg-emerald-50 text-emerald-700 border-l-2 border-emerald-600 font-bold' : 'text-slate-500 hover:bg-gray-50' }}">
                        <i class="w-4 text-xs text-center fas fa-sitemap" aria-hidden="true"></i>
                        <span class="text-xs">New Organization Reg.</span>
                    </a>

                    <div class="px-3 pt-4 pb-1 mt-4 border-t border-gray-100">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Administrator</p>
                    </div>
                    
                    <a href="{{ route('users.index') }}"
                       class="flex items-center space-x-2.5 px-3 py-2 rounded-lg transition-all {{ request()->routeIs('users.*') ? 'bg-slate-800 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100' }}">
                        <i class="w-4 text-xs text-center fas fa-users-cog" aria-hidden="true"></i>
                        <span class="text-xs font-bold">User Access</span>
                    </a>

                    <a href="{{ route('announcements.index') }}"
                       class="flex items-center space-x-2.5 px-3 py-2 rounded-lg transition-all {{ request()->routeIs('announcements.*') ? 'bg-slate-800 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100' }}">
                        <i class="w-4 text-xs text-center text-indigo-500 fas fa-bullhorn" aria-hidden="true"></i>
                        <span class="text-xs font-bold">Announcements</span>
                    </a>
                @endif
            </nav>

            {{-- User Profile Footer --}}
            <div class="relative p-3 border-t border-emerald-50 bg-slate-50/50" x-data="{ userMenuOpen: false }">
                
                {{-- Sign Out Dropdown Popover --}}
                <div x-show="userMenuOpen" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
                     x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2"
                     @click.away="userMenuOpen = false"
                     style="display: none;"
                     class="absolute left-3 right-3 bottom-full z-50 p-1.5 mb-2 bg-white border rounded-xl shadow-xl border-slate-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center justify-between w-full px-2.5 py-2 text-red-600 transition-all rounded-lg hover:bg-red-50 group">
                            <div class="flex items-center space-x-2">
                                <i class="text-xs transition-transform fas fa-sign-out-alt group-hover:-translate-x-1" aria-hidden="true"></i>
                                <span class="text-[11px] font-black tracking-wider uppercase">Sign Out</span>
                            </div>
                            <i class="fas fa-chevron-right text-[9px] opacity-30" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>

                {{-- Profile Trigger Button --}}
                <button @click="userMenuOpen = !userMenuOpen" 
                        :aria-expanded="userMenuOpen"
                        aria-label="User Account Menu"
                        class="flex items-center justify-between w-full p-1.5 rounded-lg transition-colors hover:bg-slate-100/80 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    <div class="flex items-center min-w-0 space-x-2.5">
                        <div class="flex items-center justify-center shrink-0 h-7 w-7 rounded-full bg-emerald-600 text-white text-[9px] font-bold shadow-sm shadow-emerald-200">
                            {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0 text-left">
                            <p class="text-[9px] font-black text-slate-800 truncate uppercase leading-tight">{{ auth()->user()?->name ?? 'Guest User' }}</p>
                            <p class="text-[8px] font-bold text-slate-400 truncate tracking-tighter">{{ auth()->user()?->email ?? '' }}</p>
                        </div>
                    </div>
                    <i class="fas fa-ellipsis-v text-[10px] text-slate-400 ml-1 transition-transform duration-200" :class="userMenuOpen ? 'rotate-90 text-emerald-600' : ''" aria-hidden="true"></i>
                </button>
            </div>
        </aside>

        {{-- Content Wrapper --}}
        <div class="relative flex flex-col flex-1 min-w-0 overflow-hidden">
            
            {{-- Top Navbar --}}
            <header class="sticky top-0 z-40 flex items-center justify-between h-14 px-4 bg-white border-b border-gray-200 md:px-6 shrink-0">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" aria-label="Open Sidebar Menu" class="p-1.5 -ml-1 text-gray-500 transition-colors md:hidden hover:text-emerald-600">
                        <i class="text-lg fas fa-bars" aria-hidden="true"></i>
                    </button>
                    <div class="ml-2 md:ml-0">
                        <span class="hidden text-xs font-bold tracking-widest text-gray-400 uppercase lg:inline-block">
                            National Management Information System
                        </span>
                        <span class="text-xs font-bold text-gray-400 uppercase lg:hidden">4-H LSA</span>
                    </div>
                </div>

                {{-- Right Side Actions & Role Badge --}}
                <div class="flex items-center space-x-3">
                    
                    {{-- Quick Action Agri-Resume Preview --}}
                    @if (auth()->user()?->role === 'Member')
                        <a href="{{ route('member.agri-resume.preview') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg text-[11px] font-black uppercase tracking-wider transition-all shadow-sm">
                            <i class="fa-solid fa-file-invoice text-emerald-600" aria-hidden="true"></i>
                            <span class="hidden md:inline">Agri-Resume Preview</span>
                        </a>
                    @elseif (isset($member))
                        <a href="{{ route('members.agri-resume', $member) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg text-[11px] font-black uppercase tracking-wider transition-all shadow-sm">
                            <i class="fa-solid fa-file-invoice text-emerald-600" aria-hidden="true"></i>
                            <span class="hidden md:inline">Agri-Resume Preview</span>
                        </a>
                    @endif

                    {{-- Role & Scope Badge --}}
                    <div class="hidden sm:flex items-center space-x-2 px-2.5 py-1 bg-gray-50 border border-gray-200 rounded-full">
                        <div class="h-2 w-2 rounded-full {{ auth()->user()?->role === 'Admin' ? 'bg-indigo-500 animate-pulse' : 'bg-emerald-500' }}"></div>
                        <span class="text-[9px] font-black text-gray-600 uppercase leading-snug">
                            ROLE: {{ auth()->user()?->role ?? 'N/A' }} | 
                            POS: {{ auth()->user()?->position ?? 'N/A' }} | 
                            @if(auth()->user()?->region || auth()->user()?->Region)
                                REGION: {{ auth()->user()->region->name ?? auth()->user()->Region->name }}
                            @else
                                SCOPE: Global Oversight
                            @endif
                        </span>
                    </div>
                </div>
            </header>

            {{-- Main Content Scroll Area --}}
            <main class="flex-1 overflow-y-auto focus:outline-none bg-gray-50/50">
                @isset($header)
                    <div class="px-4 py-5 bg-white border-b border-gray-200 md:px-6">
                        <h2 class="text-xl font-black tracking-tight text-gray-900">
                            {{ $header }}
                        </h2>
                    </div>
                @endisset

                <div class="p-4 md:p-6">
                    <div class="mx-auto max-w-7xl">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
    </style>

</body>
</html>