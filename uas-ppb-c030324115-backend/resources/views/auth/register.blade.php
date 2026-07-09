<x-layouts.app>
    <x-slot name="title">Daftar Akun - Pendaftaran Servis Laptop</x-slot>

    <div class="flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-6">
            <!-- Header Card -->
            <div class="text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-white">
                    Pendaftaran Akun Baru
                </h2>
                <p class="mt-2 text-sm text-slate-400">
                    Atau
                    <a href="{{ route('login') }}" class="font-medium text-indigo-400 hover:text-indigo-300 transition duration-150">
                        masuk dengan akun yang sudah ada
                    </a>
                </p>
            </div>

            <!-- Register Form Card -->
            <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-8 shadow-2xl backdrop-blur-md">
                <form class="space-y-5" action="{{ route('register') }}" method="POST">
                    @csrf

                    <!-- Name Input -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-300">
                            Nama Lengkap
                        </label>
                        <div class="mt-1">
                            <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}"
                                class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-500 transition duration-150 outline-none"
                                placeholder="Nama Anda">
                        </div>
                        @error('name')
                            <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300">
                            Alamat Email
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                                class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-500 transition duration-150 outline-none"
                                placeholder="nama@email.com">
                        </div>
                        @error('email')
                            <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Role Dropdown -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-300">
                            Daftar Sebagai
                        </label>
                        <div class="mt-1">
                            <select id="role" name="role" required
                                class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-500 transition duration-150 outline-none cursor-pointer">
                                <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer (Pelanggan)</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin / Teknisi</option>
                            </select>
                        </div>
                        @error('role')
                            <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300">
                            Password
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="new-password" required
                                class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-500 transition duration-150 outline-none"
                                placeholder="••••••••">
                        </div>
                        @error('password')
                            <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Confirmation Input -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-300">
                            Konfirmasi Password
                        </label>
                        <div class="mt-1">
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-500 transition duration-150 outline-none"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 shadow-lg shadow-indigo-500/15 hover:shadow-indigo-500/25 transition duration-150 ease-in-out cursor-pointer">
                            Daftar Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
