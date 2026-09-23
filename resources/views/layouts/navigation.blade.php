<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-40">
    {{-- Left Side: Mobile Toggle & Title --}}
    <div class="flex items-center">
        {{-- Hamburger for Mobile --}}
        <button @click="sidebarOpen = true" class="md:hidden mr-4 text-gray-500 focus:outline-none">
            <i class="fas fa-bars text-xl"></i>
        </button>

        <h1 class="text-xs font-bold text-gray-400 uppercase tracking-[0.15em] hidden sm:block">
            Information System for 4-H Club of the Philippines
        </h1>
    </div>

    {{-- Right Side: Agri-Resume Button, Role Badge & Quick Actions --}}
    <div class="flex items-center space-x-3">
        
        {{-- Agri-Resume Quick Action Button --}}
        @if (auth()->user()->role === 'Member')
            <a href="{{ route('member.agri-resume.preview') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-black uppercase tracking-wider transition-all shadow-sm">
                <i class="fa-solid fa-file-invoice text-emerald-600"></i>
                <span class="hidden md:inline">Agri-Resume</span>
            </a>
        @elseif (isset($member))
            <a href="{{ route('members.agri-resume.show', $member) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-black uppercase tracking-wider transition-all shadow-sm">
                <i class="fa-solid fa-file-invoice text-emerald-600"></i>
                <span class="hidden md:inline">Agri-Resume</span>
            </a>
        @endif

        {{-- Role Indicator Badge --}}
        <div class="flex items-center space-x-2 px-3 py-1 bg-gray-50 border border-gray-200 rounded-lg shadow-sm">
            <div class="h-2 w-2 rounded-full {{ auth()->user()->role === 'Admin' ? 'bg-indigo-500 animate-pulse' : 'bg-green-500' }}"></div>
            <span class="text-[11px] font-bold text-gray-600 uppercase tracking-tighter">
                {{ auth()->user()->role }} Mode
            </span>
        </div>

        <div class="h-6 w-px bg-gray-200 mx-1"></div>

        {{-- Log Out Trigger --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="text-xs font-bold text-gray-400 hover:text-red-500 uppercase tracking-widest transition-colors pl-1">
                Logout
            </button>
        </form>
    </div>
</header>