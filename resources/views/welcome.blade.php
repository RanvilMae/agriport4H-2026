<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>4-H Club of the Philippines | Empowering Youth in Agriculture</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,800,900&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-800 bg-slate-50 selection:bg-emerald-500 selection:text-white" x-data="{ mobileMenuOpen: false }">

    {{-- Top Navigation Bar --}}
    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-emerald-100/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                {{-- Brand Logo --}}
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo.png') }}" class="w-12 h-12 object-contain" alt="4-H Club Philippines Logo">
                    <div>
                        <span class="text-lg font-black tracking-tight text-slate-800 block leading-none">4-H CLUB <span class="text-emerald-600">PHILIPPINES</span></span>
                        <span class="text-[10px] font-bold text-emerald-700 tracking-widest uppercase">Youth Development & Agriculture</span>
                    </div>
                </div>

                {{-- Desktop Nav Links --}}
                <div class="hidden md:flex items-center space-x-8 text-xs font-bold uppercase tracking-wider text-slate-600">
                    <a href="#about" class="hover:text-emerald-600 transition-colors">About Us</a>
                    <a href="#pledge" class="hover:text-emerald-600 transition-colors">4-H Pledge</a>
                    <a href="#programs" class="hover:text-emerald-600 transition-colors">Key Programs</a>
                    <a href="#impact" class="hover:text-emerald-600 transition-colors">Our Impact</a>
                </div>

                {{-- Auth Buttons --}}
                <div class="hidden md:flex items-center space-x-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-md shadow-emerald-200 hover:bg-emerald-700 transition-all flex items-center space-x-2">
                                <span>Go to Dashboard</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-slate-600 hover:text-emerald-600 font-bold text-xs transition-colors">
                                Sign In
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-md shadow-emerald-200 hover:bg-emerald-700 transition-all">
                                    Join 4-H Club
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>

                {{-- Mobile Menu Trigger --}}
                <div class="flex md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-slate-600 hover:text-emerald-600">
                        <i class="fas" :class="mobileMenuOpen ? 'fa-times text-xl' : 'fa-bars text-xl'"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Navigation Dropdown --}}
        <div x-show="mobileMenuOpen" x-transition class="md:hidden border-b border-gray-200 bg-white px-4 pt-2 pb-6 space-y-3">
            <a href="#about" @click="mobileMenuOpen = false" class="block py-2 text-sm font-bold text-slate-700 hover:text-emerald-600">About Us</a>
            <a href="#pledge" @click="mobileMenuOpen = false" class="block py-2 text-sm font-bold text-slate-700 hover:text-emerald-600">4-H Pledge</a>
            <a href="#programs" @click="mobileMenuOpen = false" class="block py-2 text-sm font-bold text-slate-700 hover:text-emerald-600">Key Programs</a>
            <a href="#impact" @click="mobileMenuOpen = false" class="block py-2 text-sm font-bold text-slate-700 hover:text-emerald-600">Our Impact</a>
            <div class="pt-4 border-t border-gray-100 flex flex-col space-y-2">
                @auth
                    <a href="{{ url('/dashboard') }}" class="w-full text-center py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">Sign In</a>
                    <a href="{{ route('register') }}" class="w-full text-center py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs">Join 4-H Club</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-b from-emerald-50/60 via-slate-50 to-white py-20 lg:py-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                {{-- Hero Text --}}
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full bg-emerald-100/80 border border-emerald-200 text-emerald-800 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Official Youth Organization of DA-ATI</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-none">
                        Empowering <span class="text-emerald-600">Filipino Youth</span> for Sustainable Agriculture.
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 font-medium leading-relaxed">
                        The 4-H Club of the Philippines nurtures young leaders through hands-on learning in Head, Heart, Hands, and Health—driving rural development, food security, and agri-entrepreneurship.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start space-y-3 sm:space-y-0 sm:space-x-4 pt-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-emerald-600 text-white font-black text-sm shadow-xl shadow-emerald-200 hover:bg-emerald-700 hover:-translate-y-0.5 transition-all text-center">
                                Access Portal
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-emerald-600 text-white font-black text-sm shadow-xl shadow-emerald-200 hover:bg-emerald-700 hover:-translate-y-0.5 transition-all text-center">
                                Become a Member
                            </a>
                            <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50 transition-all text-center shadow-sm">
                                Member Sign In
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Hero Visual / Emblem Card --}}
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-md bg-white p-8 rounded-3xl border border-emerald-100 shadow-2xl shadow-emerald-950/5 text-center">
                        <div class="w-24 h-24 mx-auto mb-6 p-3 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center justify-center shadow-inner">
                            <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-contain" alt="4-H Emblem">
                        </div>
                        <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Make the Best Better</h3>
                        <p class="text-xs font-semibold text-emerald-600 mt-1 uppercase tracking-widest">National Motto</p>
                        <hr class="my-6 border-slate-100">
                        <div class="grid grid-cols-2 gap-3 text-left">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase">Target Youth</p>
                                <p class="text-xs font-bold text-slate-800">10 – 30 Years Old</p>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase">Focus Areas</p>
                                <p class="text-xs font-bold text-slate-800">Agri & Innovation</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- The 4-H Pillars Section --}}
    <section id="pledge" class="py-20 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <p class="text-xs font-black text-emerald-600 uppercase tracking-widest">The Core Pillars</p>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight sm:text-4xl">What the 4-H Stands For</h2>
                <p class="text-sm text-slate-600 font-medium">Every member pledges their Head, Heart, Hands, and Health to the betterment of their club, community, and country.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- HEAD --}}
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-200 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 mb-4 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-lg shadow-md group-hover:scale-110 transition-transform">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-800 mb-1">HEAD</h3>
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2">Clearer Thinking</p>
                    <p class="text-xs text-slate-600 leading-relaxed">Developing critical thinking, agricultural knowledge, management skills, and innovative solutions for farming challenges.</p>
                </div>

                {{-- HEART --}}
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-200 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 mb-4 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-lg shadow-md group-hover:scale-110 transition-transform">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-800 mb-1">HEART</h3>
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2">Greater Loyalty</p>
                    <p class="text-xs text-slate-600 leading-relaxed">Fostering leadership, civic responsibility, deep appreciation for nature, and strong bonds within communities.</p>
                </div>

                {{-- HANDS --}}
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-200 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 mb-4 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-lg shadow-md group-hover:scale-110 transition-transform">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-800 mb-1">HANDS</h3>
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2">Larger Service</p>
                    <p class="text-xs text-slate-600 leading-relaxed">Promoting vocational skills, hands-on farming techniques, community service, and sustainable livelihood practices.</p>
                </div>

                {{-- HEALTH --}}
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-200 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 mb-4 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-lg shadow-md group-hover:scale-110 transition-transform">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-800 mb-1">HEALTH</h3>
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2">Better Living</p>
                    <p class="text-xs text-slate-600 leading-relaxed">Advocating for active lifestyles, mental wellness, food security, and healthy living for a productive society.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- Key Programs Section --}}
    <section id="programs" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <p class="text-xs font-black text-emerald-600 uppercase tracking-widest">Growth Opportunities</p>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight sm:text-4xl">Youth Agricultural Programs</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center mb-6">
                        <i class="fas fa-seedling text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Agri-Enterprise Development</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-4">Training youth to transition from traditional farming to modern, profitable agribusiness enterprises through micro-grants and mentorship.</p>
                </div>

                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center mb-6">
                        <i class="fas fa-globe-asia text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">International Exchange (YEP)</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-4">Young Farmers Exchange Programs (e.g., Japan/Taiwan) equipping members with cutting-edge global agricultural technologies and practices.</p>
                </div>

                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center mb-6">
                        <i class="fas fa-trophy text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">National Youth Conventions</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-4">Annual gatherings highlighting excellence through project contests, leadership summits, and regional knowledge exchanges.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- Call to Action --}}
    <section class="py-16 bg-slate-900 text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
            <h2 class="text-3xl sm:text-4xl font-black tracking-tight">Ready to lead the future of agriculture?</h2>
            <p class="text-slate-400 text-sm max-w-xl mx-auto">Register as an official 4-H member today to build your Agri-Resume, connect with local clubs, and access national development programs.</p>
            <div class="pt-2">
                <a href="{{ route('register') }}" class="inline-block px-8 py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs uppercase tracking-wider transition-colors shadow-lg shadow-emerald-500/20">
                    Get Started Now
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-slate-950 border-t border-slate-800 py-12 text-slate-400 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo.png') }}" class="w-8 h-8 opacity-80" alt="Logo">
                <p>&copy; {{ date('Y') }} 4-H Club of the Philippines. All rights reserved.</p>
            </div>
            <div class="flex space-x-6 text-slate-400">
                <a href="{{ route('login') }}" class="hover:text-emerald-400">Management Information System Log In</a>
            </div>
        </div>
    </footer>

</body>
</html>