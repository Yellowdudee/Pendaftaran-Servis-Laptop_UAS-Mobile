<x-layouts.app>
    <x-slot name="title">Login - Pendaftaran Servis Laptop</x-slot>

    <div class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header Card -->
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold tracking-tight text-white">
                    Masuk ke Akun Anda
                </h2>
                <p class="mt-2 text-sm text-slate-400">
                    Atau
                    <a href="{{ route('register') }}" class="font-medium text-indigo-400 hover:text-indigo-300 transition duration-150">
                        daftar akun baru sekarang
                    </a>
                </p>
            </div>

            <!-- Login Form Card -->
            <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-8 shadow-2xl backdrop-blur-md">
                <form class="space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300">
                            Alamat Email
                        </label>
                        <div class="mt-1 relative">
                            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                                class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-500 transition duration-150 outline-none"
                                placeholder="nama@email.com">
                        </div>
                        @error('email')
                            <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-medium text-slate-300">
                                Password
                            </label>
                        </div>
                        <div class="mt-1 relative">
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-500 transition duration-150 outline-none"
                                placeholder="••••••••">
                        </div>
                        @error('password')
                            <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500/20 focus:ring-offset-slate-900">
                        <label for="remember" class="ml-2 block text-sm text-slate-300 cursor-pointer select-none">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 shadow-lg shadow-indigo-500/15 hover:shadow-indigo-500/25 transition duration-150 ease-in-out cursor-pointer">
                            Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
