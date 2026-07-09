<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Pendaftaran Servis Laptop' }}</title>
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles and Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b0f19;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.12) 0px, transparent 50%);
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="text-slate-100 min-h-screen flex flex-col antialiased">

    <!-- Header Navigation -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo / Brand -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-white via-slate-100 to-indigo-400 bg-clip-text text-transparent">
                        Servis Laptop
                    </span>
                </div>

                <!-- Navigation Links -->
                <nav class="flex items-center space-x-4">
                    @auth
                        <div class="flex items-center space-x-4">
                            <span class="hidden md:inline-block text-sm text-slate-400">
                                Halo, <strong class="text-indigo-400 font-semibold">{{ Auth::user()->name }}</strong> 
                                <span class="ml-1.5 px-2 py-0.5 text-xs font-semibold tracking-wide rounded-full uppercase {{ Auth::user()->role === 'admin' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' }}">
                                    {{ Auth::user()->role }}
                                </span>
                            </span>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700/80 border border-slate-700/60 transition duration-150 ease-in-out cursor-pointer">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-300 hover:text-white transition duration-150">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/10 transition duration-150">Daftar</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-900 bg-slate-950/60 py-6 mt-12 text-center text-sm text-slate-500">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; Dzaki Ahmad Andreaz (C030324115)</p>
        </div>
    </footer>

    <!-- Alpine.js Flash Message Notification -->
    @if(session('success') || session('error') || $errors->any())
        <div class="fixed bottom-5 right-5 z-50 max-w-sm w-full"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 6000)"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;">
            
            @if(session('success'))
                <div class="rounded-xl border border-emerald-500/30 bg-slate-900/90 backdrop-blur-md p-4 shadow-xl shadow-emerald-500/5 flex items-start space-x-3">
                    <div class="p-1 bg-emerald-500/10 text-emerald-400 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <p class="text-sm font-semibold text-emerald-400">Sukses</p>
                        <p class="text-xs text-slate-300 mt-1">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-200 focus:outline-none transition cursor-pointer">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="rounded-xl border border-rose-500/30 bg-slate-900/90 backdrop-blur-md p-4 shadow-xl shadow-rose-500/5 flex items-start space-x-3">
                    <div class="p-1 bg-rose-500/10 text-rose-400 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <p class="text-sm font-semibold text-rose-400">Terjadi Kesalahan</p>
                        <p class="text-xs text-slate-300 mt-1">
                            @if(session('error'))
                                {{ session('error') }}
                            @else
                                Harap periksa kembali inputan Anda.
                            @endif
                        </p>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-200 focus:outline-none transition cursor-pointer">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

        </div>
    @endif

</body>
</html>
