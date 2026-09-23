<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>4-H Club Philippines | Information System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .clover-bg {
            background-color: #15803d; /* Emerald Green */
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        /* Custom scrollbar styling for internal panel scrolling */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 4px;
        }
    </style>
</head>
<body class="h-screen overflow-hidden antialiased bg-slate-50 text-slate-900 selection:bg-emerald-500 selection:text-white">

    <div class="grid h-screen w-screen overflow-hidden grid-cols-1 lg:grid-cols-12">

        {{-- LEFT PANEL: 4-H Branding & Identity Showcase --}}
        <div class="relative flex-col justify-between hidden h-full p-8 xl:p-12 overflow-hidden text-white lg:flex lg:col-span-7 clover-bg">
            
            {{-- Header/Logo --}}
            <div class="relative z-10 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white rounded-2xl shadow-lg">
                        <img src="{{ asset('images/logo.png') }}" class="object-contain w-10 h-10" alt="4-H Official Logo">
                    </div>
                    <div>
                        <h2 class="text-xl font-black tracking-tight leading-none">4-H CLUB</h2>
                        <span class="text-[10px] font-extrabold uppercase tracking-[0.3em] text-emerald-200">Philippines</span>
                    </div>
                </div>
                <span class="px-4 py-1.5 text-xs font-black tracking-wider uppercase border rounded-full border-white/20 bg-white/10 backdrop-blur-md">
                    Est. 1952
                </span>
            </div>

            {{-- Dynamic Content Area --}}
            <div class="relative z-10 my-auto py-6 overflow-y-auto custom-scrollbar">
                {{-- WELCOME CONTENT --}}
                <div id="welcome-showcase" class="space-y-6 transition-all duration-500">
                    <div class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-emerald-900 bg-emerald-300 rounded-full">
                        <i class="fa-solid fa-seedling"></i>
                        <span>Empowering Rural Youth for Agricultural Leadership</span>
                    </div>

                    <h1 class="text-4xl xl:text-5xl font-black tracking-tight leading-tight">
                        To Make the <br/>
                        <span class="text-emerald-300 underline decoration-emerald-400 decoration-wavy decoration-2">Best Better.</span>
                    </h1>

                    <p class="max-w-xl text-sm xl:text-base font-medium text-emerald-100/90 leading-relaxed">
                        Join thousands of young agricultural enthusiasts across the Philippines in developing critical life skills, innovative farming practices, and community leadership through 4-H programs.
                    </p>

                    {{-- 4-H Pillars --}}
                    <div class="grid grid-cols-2 gap-3 pt-2 max-w-lg">
                        <div class="p-3.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/10">
                            <i class="fa-solid fa-brain text-emerald-300 text-base mb-1"></i>
                            <h4 class="font-extrabold text-xs uppercase tracking-wider">Head</h4>
                            <p class="text-[11px] text-emerald-100/80 mt-0.5">Clearer thinking & decision making</p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/10">
                            <i class="fa-solid fa-heart text-emerald-300 text-base mb-1"></i>
                            <h4 class="font-extrabold text-xs uppercase tracking-wider">Heart</h4>
                            <p class="text-[11px] text-emerald-100/80 mt-0.5">Greater loyalty & community connection</p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/10">
                            <i class="fa-solid fa-hand-holding-heart text-emerald-300 text-base mb-1"></i>
                            <h4 class="font-extrabold text-xs uppercase tracking-wider">Hands</h4>
                            <p class="text-[11px] text-emerald-100/80 mt-0.5">Larger service & practical skills</p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/10">
                            <i class="fa-solid fa-child-reaching text-emerald-300 text-base mb-1"></i>
                            <h4 class="font-extrabold text-xs uppercase tracking-wider">Health</h4>
                            <p class="text-[11px] text-emerald-100/80 mt-0.5">Better living for sustainable youth</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Info --}}
            <div class="relative z-10 flex items-center justify-between text-xs font-semibold text-emerald-200 border-t border-white/10 pt-4 shrink-0">
                <span>&copy; {{ date('Y') }} 4-H Club Information System</span>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-white transition-colors"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="hover:text-white transition-colors"><i class="fa-solid fa-globe"></i></a>
                </div>
            </div>

            {{-- Background Glow --}}
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        {{-- RIGHT PANEL: Navigation & Forms --}}
        <div class="flex flex-col justify-between h-full p-6 sm:p-10 bg-white lg:col-span-5 overflow-y-auto custom-scrollbar">
            
            {{-- Top Toggle Nav --}}
            <div class="flex items-center justify-between mb-6 shrink-0">
                <div class="flex lg:hidden items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" class="w-8 h-8" alt="4-H Logo">
                    <span class="font-black text-slate-800 tracking-tight">4-H Club</span>
                </div>
                
                <div class="flex bg-slate-100 p-1.5 rounded-full text-xs font-black ml-auto">
                    <button id="nav-welcome-btn" onclick="switchView('welcome')" class="px-4 py-2 rounded-full transition-all text-slate-600 hover:text-slate-900">
                        Welcome
                    </button>
                    <button id="nav-login-btn" onclick="switchView('login')" class="px-4 py-2 rounded-full transition-all bg-emerald-700 text-white shadow-md">
                        Sign In
                    </button>
                </div>
            </div>

            <div class="my-auto max-w-md mx-auto w-full py-4">

                {{-- WELCOME TAB VIEW --}}
                <div id="view-welcome" class="hidden space-y-6">
                    <div>
                        <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-700 bg-emerald-100 rounded-full">
                            Youth Development Portal
                        </span>
                        <h2 class="mt-3 text-2xl xl:text-3xl font-black text-slate-900 tracking-tight">
                            Welcome to 4-H Philippines
                        </h2>
                        <p class="mt-2 text-xs xl:text-sm text-slate-600 leading-relaxed">
                            Access our centralized information system designed for club members, officers, and agricultural youth leaders nationwide.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <button onclick="switchView('login')" class="w-full py-3.5 px-5 bg-emerald-700 hover:bg-emerald-800 text-white font-black rounded-2xl shadow-lg shadow-emerald-900/10 transition-all flex items-center justify-between group">
                            <span>Log Into Member Account</span>
                            <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="w-full py-3.5 px-5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold rounded-2xl transition-all flex items-center justify-between">
                                <span>Register New Club / Member</span>
                                <i class="fa-solid fa-user-plus text-slate-400"></i>
                            </a>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <h4 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2.5">Key Features</h4>
                        <ul class="space-y-2 text-xs font-bold text-slate-600">
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                Member & Youth Leader Directory
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                Project & Agricultural Activity Tracking
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                Regional Event & Convention Registration
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- LOGIN FORM TAB VIEW --}}
                <div id="view-login" class="space-y-5">
                    <div>
                        <h2 class="text-2xl xl:text-3xl font-black tracking-tight text-slate-900">Member Login</h2>
                        <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-widest mt-1">
                            Youth Development Portal
                        </p>
                    </div>

                    {{-- Flash Status Message --}}
                    @if (session('status'))
                        <div class="flex items-center gap-3 p-3 text-xs font-bold border text-emerald-700 border-emerald-100 rounded-2xl bg-emerald-50" role="alert">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    {{-- General Alert for Unhandled Validation Failures --}}
                    @if ($errors->any())
                        <div class="flex flex-col gap-2 p-3 text-xs font-bold text-red-600 border border-red-100 rounded-2xl bg-red-50/50" role="alert">
                            <div class="flex items-center gap-3">
                                <i class="text-sm fa-solid fa-shield-virus"></i>
                                <span>Please check your credentials and try again.</span>
                            </div>
                        </div>
                    @endif

                    {{-- Authentication Form --}}
                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        {{-- Email Field --}}
                        <div class="space-y-1">
                            <label for="email" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                Member Email
                            </label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-emerald-700 transition-colors">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input 
                                    id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    autofocus 
                                    autocomplete="username"
                                    placeholder="name@domain.com"
                                    class="w-full pl-11 pr-5 py-3 bg-slate-50 border @error('email') border-red-400 focus:ring-red-500/10 @else border-slate-200 focus:ring-emerald-600/10 focus:border-emerald-600 @enderror rounded-2xl focus:bg-white focus:ring-4 transition-all outline-none font-bold text-xs text-slate-800"
                                >
                            </div>
                            @error('email')
                                <p class="mt-1 ml-1 text-xs font-bold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password Field with Show/Hide Toggle --}}
                        <div class="space-y-1">
                            <div class="flex items-center justify-between ml-1">
                                <label for="password" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Password
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-[10px] font-black text-emerald-700 hover:text-emerald-800 uppercase tracking-widest transition-colors">
                                        Forgot Password?
                                    </a>
                                @endif
                            </div>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-emerald-700 transition-colors">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="w-full pl-11 pr-11 py-3 bg-slate-50 border @error('password') border-red-400 focus:ring-red-500/10 @else border-slate-200 focus:ring-emerald-600/10 focus:border-emerald-600 @enderror rounded-2xl focus:bg-white focus:ring-4 transition-all outline-none font-bold text-xs text-slate-800"
                                >
                                <button 
                                    type="button" 
                                    onclick="togglePasswordVisibility()" 
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 focus:outline-none"
                                    title="Show/Hide Password"
                                >
                                    <i id="password-toggle-icon" class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 ml-1 text-xs font-bold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Remember Me & Show Password Checkbox Options --}}
                        <div class="flex items-center justify-between px-1">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="remember" class="w-3.5 h-3.5 text-emerald-700 rounded border-slate-300 focus:ring-emerald-600">
                                <span class="ml-2 text-xs font-bold text-slate-600">Remember session</span>
                            </label>

                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="show-password-checkbox" onchange="togglePasswordVisibility(this.checked)" class="w-3.5 h-3.5 text-emerald-700 rounded border-slate-300 focus:ring-emerald-600">
                                <span class="ml-2 text-xs font-bold text-slate-600">Show password</span>
                            </label>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="w-full py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white font-black rounded-2xl shadow-lg shadow-emerald-900/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-3 uppercase tracking-[0.15em] text-xs">
                            <span>Access Portal</span>
                            <i class="text-xs fa-solid fa-right-to-bracket"></i>
                        </button>
                    </form>

                    {{-- Registration Link --}}
                    <div class="pt-2 text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                            New to 4-H Club?
                        </p>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="mt-1.5 inline-flex items-center gap-2 px-5 py-2 text-[10px] font-black text-emerald-800 uppercase tracking-wider transition-all border border-emerald-200 rounded-full hover:bg-emerald-50">
                                <span>Create Member Account</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Mobile Footer --}}
            <div class="text-center text-xs text-slate-400 pt-4 shrink-0 lg:hidden">
                &copy; {{ date('Y') }} 4-H Club Philippines
            </div>
        </div>

    </div>

    {{-- Interactive Scripts --}}
    <script>
        function switchView(view) {
            const welcomeView = document.getElementById('view-welcome');
            const loginView = document.getElementById('view-login');
            const welcomeBtn = document.getElementById('nav-welcome-btn');
            const loginBtn = document.getElementById('nav-login-btn');

            if (view === 'welcome') {
                welcomeView.classList.remove('hidden');
                loginView.classList.add('hidden');
                
                welcomeBtn.classList.add('bg-emerald-700', 'text-white', 'shadow-md');
                welcomeBtn.classList.remove('text-slate-600');
                
                loginBtn.classList.remove('bg-emerald-700', 'text-white', 'shadow-md');
                loginBtn.classList.add('text-slate-600');
            } else {
                loginView.classList.remove('hidden');
                welcomeView.classList.add('hidden');
                
                loginBtn.classList.add('bg-emerald-700', 'text-white', 'shadow-md');
                loginBtn.classList.remove('text-slate-600');
                
                welcomeBtn.classList.remove('bg-emerald-700', 'text-white', 'shadow-md');
                welcomeBtn.classList.add('text-slate-600');
            }
        }

        function togglePasswordVisibility(forceState) {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle-icon');
            const checkbox = document.getElementById('show-password-checkbox');

            let isVisible = forceState !== undefined 
                ? forceState 
                : passwordInput.type === 'password';

            passwordInput.type = isVisible ? 'text' : 'password';
            
            // Sync eye icon
            if (isVisible) {
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }

            // Sync checkbox state if toggled via icon
            if (forceState === undefined) {
                checkbox.checked = isVisible;
            }
        }
    </script>
</body>
</html>