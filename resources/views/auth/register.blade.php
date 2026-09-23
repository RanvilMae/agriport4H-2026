<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>4-H Club Philippines | Registration</title>

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

            {{-- Center Visual Content --}}
            <div class="relative z-10 my-auto py-6 overflow-y-auto custom-scrollbar text-center">
                <div class="inline-block p-6 bg-white rounded-3xl shadow-2xl mb-6 transform hover:rotate-1 transition-transform duration-500">
                    <img src="{{ asset('images/logo.png') }}" class="object-contain w-32 h-32 xl:w-40 xl:h-40" alt="4-H Official Logo">
                </div>
                <h1 class="text-3xl xl:text-5xl font-black tracking-tight leading-tight uppercase">
                    Join the 4-H Club <br/>
                    <span class="text-emerald-300">of the Philippines</span>
                </h1>
                <p class="mt-4 text-xs xl:text-sm font-extrabold uppercase tracking-[0.3em] text-emerald-100/90">
                    Head • Heart • Hands • Health
                </p>
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

        {{-- RIGHT PANEL: Registration Form --}}
        <div class="flex flex-col justify-between h-full p-6 sm:p-10 bg-white lg:col-span-5 overflow-y-auto custom-scrollbar">
            
            {{-- Mobile Header --}}
            <div class="flex lg:hidden items-center justify-between mb-4 shrink-0">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" class="w-8 h-8" alt="4-H Logo">
                    <span class="font-black text-slate-800 tracking-tight">4-H Club</span>
                </div>
                <a href="{{ route('login') }}" class="text-xs font-black text-emerald-700 uppercase tracking-wider">
                    Sign In
                </a>
            </div>

            <div class="my-auto max-w-md mx-auto w-full py-4">

                {{-- Alert Section --}}
                @if (session('success'))
                    <div class="flex items-center gap-3 p-3 mb-4 text-xs font-bold border shadow-sm border-emerald-100 rounded-2xl bg-emerald-50 text-emerald-700">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-3 mb-4 text-xs font-bold text-red-600 border border-red-100 rounded-2xl bg-red-50/50">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                            <span class="uppercase text-[10px] tracking-widest font-black opacity-80">Registration Errors</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Section Title --}}
                <div class="mb-5">
                    <h2 class="text-2xl xl:text-3xl font-black tracking-tight text-slate-900">New Account</h2>
                    <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-widest mt-0.5">
                        Registration Portal
                    </p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                    @csrf

                    {{-- Member ID --}}
                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Member ID</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-emerald-700 transition-colors">
                                <i class="fa-solid fa-id-card"></i>
                            </span>
                            <input 
                                type="text" 
                                name="member_id" 
                                value="{{ old('member_id') }}" 
                                required 
                                placeholder="e.g. 4H-2024-001"
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border @error('member_id') border-red-400 focus:ring-red-500/10 @else border-slate-200 focus:ring-emerald-600/10 focus:border-emerald-600 @enderror rounded-2xl focus:bg-white focus:ring-4 transition-all outline-none font-bold text-xs text-slate-800"
                            >
                        </div>
                        <x-input-error :messages="$errors->get('member_id')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        {{-- Full Name --}}
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Full Name</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-emerald-700 transition-colors">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input 
                                    type="text" 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    required 
                                    placeholder="Juan Dela Cruz"
                                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border @error('name') border-red-400 focus:ring-red-500/10 @else border-slate-200 focus:ring-emerald-600/10 focus:border-emerald-600 @enderror rounded-2xl focus:bg-white focus:ring-4 transition-all outline-none font-bold text-xs text-slate-800"
                                >
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        {{-- Email --}}
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Email</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-emerald-700 transition-colors">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    placeholder="name@domain.com"
                                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border @error('email') border-red-400 focus:ring-red-500/10 @else border-slate-200 focus:ring-emerald-600/10 focus:border-emerald-600 @enderror rounded-2xl focus:bg-white focus:ring-4 transition-all outline-none font-bold text-xs text-slate-800"
                                >
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>
                    </div>

                    {{-- System Role --}}
                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">System Role</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-emerald-700 transition-colors">
                                <i class="fa-solid fa-user-shield"></i>
                            </span>
                            <select name="role" required class="w-full pl-11 pr-8 py-2.5 bg-slate-50 border @error('role') border-red-400 focus:ring-red-500/10 @else border-slate-200 focus:ring-emerald-600/10 focus:border-emerald-600 @enderror rounded-2xl focus:bg-white focus:ring-4 transition-all outline-none font-bold text-xs text-slate-800 appearance-none">
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select Role</option>
                                <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="President" {{ old('role') == 'President' ? 'selected' : '' }}>President</option>
                                <option value="Coordinator" {{ old('role') == 'Coordinator' ? 'selected' : '' }}>Coordinator</option>
                                <option value="Member" {{ old('role') == 'Member' ? 'selected' : '' }}>Member</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </span>
                        </div>
                        <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        {{-- Region --}}
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Region</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-emerald-700 transition-colors">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                </span>
                                <select name="region_id" class="w-full pl-11 pr-8 py-2.5 bg-slate-50 border @error('region_id') border-red-400 focus:ring-red-500/10 @else border-slate-200 focus:ring-emerald-600/10 focus:border-emerald-600 @enderror rounded-2xl focus:bg-white focus:ring-4 transition-all outline-none font-bold text-xs text-slate-800 appearance-none">
                                    <option value="">Select Region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </span>
                            </div>
                            <x-input-error :messages="$errors->get('region_id')" class="mt-1" />
                        </div>

                        {{-- Position --}}
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Position</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-emerald-700 transition-colors">
                                    <i class="fa-solid fa-briefcase"></i>
                                </span>
                                <input 
                                    type="text" 
                                    name="position" 
                                    value="{{ old('position') }}" 
                                    placeholder="e.g. SDU Head"
                                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border @error('position') border-red-400 focus:ring-red-500/10 @else border-slate-200 focus:ring-emerald-600/10 focus:border-emerald-600 @enderror rounded-2xl focus:bg-white focus:ring-4 transition-all outline-none font-bold text-xs text-slate-800"
                                >
                            </div>
                            <x-input-error :messages="$errors->get('position')" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        {{-- Password --}}
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Password</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-emerald-700 transition-colors">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    placeholder="••••••••"
                                    class="w-full pl-11 pr-10 py-2.5 bg-slate-50 border @error('password') border-red-400 focus:ring-red-500/10 @else border-slate-200 focus:ring-emerald-600/10 focus:border-emerald-600 @enderror rounded-2xl focus:bg-white focus:ring-4 transition-all outline-none font-bold text-xs text-slate-800"
                                >
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        {{-- Confirm Password --}}
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Confirm</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-emerald-700 transition-colors">
                                    <i class="fa-solid fa-lock-check"></i>
                                </span>
                                <input 
                                    id="password_confirmation" 
                                    type="password" 
                                    name="password_confirmation" 
                                    required 
                                    placeholder="••••••••"
                                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:ring-emerald-600/10 focus:border-emerald-600 rounded-2xl focus:bg-white focus:ring-4 transition-all outline-none font-bold text-xs text-slate-800"
                                >
                            </div>
                        </div>
                    </div>

                    {{-- Show Password Checkbox --}}
                    <div class="flex items-center justify-end px-1 pt-1">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="show-password-checkbox" onchange="toggleRegistrationPasswords(this.checked)" class="w-3.5 h-3.5 text-emerald-700 rounded border-slate-300 focus:ring-emerald-600">
                            <span class="ml-2 text-xs font-bold text-slate-600">Show passwords</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="w-full py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white font-black rounded-2xl shadow-lg shadow-emerald-900/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-3 uppercase tracking-[0.15em] text-xs mt-2">
                        <span>Register Account</span>
                        <i class="text-xs fa-solid fa-user-plus"></i>
                    </button>

                    {{-- Sign In Redirect --}}
                    <div class="pt-2 text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                            Already registered?
                        </p>
                        <a href="{{ route('login') }}" class="mt-1.5 inline-flex items-center gap-2 px-5 py-2 text-[10px] font-black text-emerald-800 uppercase tracking-wider transition-all border border-emerald-200 rounded-full hover:bg-emerald-50">
                            <span>Sign In To Portal</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </form>

            </div>

            {{-- Mobile Footer --}}
            <div class="text-center text-xs text-slate-400 pt-2 shrink-0 lg:hidden">
                &copy; {{ date('Y') }} 4-H Club Philippines
            </div>
        </div>

    </div>

    {{-- Interactive Script --}}
    <script>
        function toggleRegistrationPasswords(show) {
            const pwd = document.getElementById('password');
            const pwdConfirm = document.getElementById('password_confirmation');
            const type = show ? 'text' : 'password';
            
            if (pwd) pwd.type = type;
            if (pwdConfirm) pwdConfirm.type = type;
        }
    </script>
</body>
</html>