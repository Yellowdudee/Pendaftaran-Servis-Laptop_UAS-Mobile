<x-layouts.app>
    <x-slot name="title">Dashboard Utama - Pendaftaran Servis Laptop</x-slot>

    <!-- Custom Style Block for x-cloak and Print Media Layout -->
    <style>
        [x-cloak] {
            display: none !important;
        }

        @media print {

            /* Hide all web components for printing */
            body>* {
                display: none !important;
            }

            /* Show only the print receipt */
            #print-receipt-section {
                display: block !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                color: #000000 !important;
                background-color: #ffffff !important;
                font-family: 'Courier New', Courier, monospace;
            }
        }
    </style>

    <!-- Main Dashboard Controller (Alpine.js) -->
    <div x-data="{
        editModalOpen: false,
        deleteModalOpen: false,
        detailModalOpen: false,
        searchQuery: '',
        activeTab: 'all',
        activeService: {
            id: '',
            device_name: '',
            serial_number: '',
            phone_number: '',
            status: 'pending',
            total_cost: '',
            formatted_cost: '-',
            technician_notes: '',
            customer_name: '',
            customer_email: '',
            created_at: '',
            complaints: ''
        },
        updateUrl: '',
        deleteUrl: '',
        services: [
            @foreach($services as $service)
            {
                id: '{{ $service->id }}',
                device_name: '{{ addslashes($service->device_name) }}',
                serial_number: '{{ addslashes($service->serial_number) }}',
                phone_number: '{{ addslashes($service->phone_number) }}',
                complaints: '{{ addslashes(str_replace(array("\r", "\n", "'"), array(" ", " ", "\'"), $service->complaints)) }}',
                status: '{{ $service->status }}',
                total_cost: '{{ $service->total_cost }}',
                formatted_cost: '{{ $service->total_cost ? "Rp " . number_format($service->total_cost, 0, ",", ".") : "Belum Dihitung" }}',
                technician_notes: '{{ addslashes(str_replace(array("\r", "\n", "'"), array(" ", " ", "\'"), $service->technician_notes ?? "")) }}',
                created_at: '{{ $service->created_at->format("d M Y, H:i") }}',
                customer_name: '{{ addslashes($service->user->name) }}',
                customer_email: '{{ addslashes($service->user->email) }}'
            },
            @endforeach
        ],

        get filteredServices() {
            return this.services.filter(s => {
                const query = this.searchQuery.toLowerCase();
                const matchesSearch = s.device_name.toLowerCase().includes(query) || 
                                      s.serial_number.toLowerCase().includes(query) || 
                                      s.customer_name.toLowerCase().includes(query) || 
                                      s.complaints.toLowerCase().includes(query) ||
                                      s.phone_number.toLowerCase().includes(query);
                
                const matchesTab = this.activeTab === 'all' || s.status === this.activeTab;
                return matchesSearch && matchesTab;
            });
        },

        openDetailModal(service) {
            this.activeService = { ...service };
            this.detailModalOpen = true;
        },

        openEditModal(service, updateUrl) {
            this.activeService = { ...service };
            this.updateUrl = updateUrl;
            this.editModalOpen = true;
        },

        openDeleteModal(serviceId, deleteUrl) {
            this.activeService.id = serviceId;
            this.deleteUrl = deleteUrl;
            this.deleteModalOpen = true;
        },

        printReceipt() {
            window.print();
        }
    }" class="space-y-8 no-print">

        <!-- ================= STATS SECTION ================= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Total Servis -->
            <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-5 shadow-lg flex items-center space-x-4">
                <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Servis</p>
                    <h3 class="text-2xl font-bold text-white mt-1">{{ $stats['total'] }} <span class="text-xs text-slate-400 font-normal">Unit</span></h3>
                </div>
            </div>

            <!-- Card 2: Active Repair -->
            <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-5 shadow-lg flex items-center space-x-4">
                <div class="p-3 bg-amber-500/10 text-amber-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sedang Diproses</p>
                    <h3 class="text-2xl font-bold text-white mt-1">{{ $stats['active'] }} <span class="text-xs text-slate-400 font-normal">Unit</span></h3>
                </div>
            </div>

            <!-- Card 3: Ready for pickup -->
            <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-5 shadow-lg flex items-center space-x-4">
                <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Selesai Servis</p>
                    <h3 class="text-2xl font-bold text-white mt-1">{{ $stats['completed'] }} <span class="text-xs text-slate-400 font-normal">Unit</span></h3>
                </div>
            </div>

            <!-- Card 4: Cost or revenue -->
            <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-5 shadow-lg flex items-center space-x-4">
                <div class="p-3 bg-purple-500/10 text-purple-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        {{ Auth::user()->role === 'admin' ? 'Total Pendapatan' : 'Total Pengeluaran' }}
                    </p>
                    <h3 class="text-lg font-bold text-indigo-300 mt-1.5">
                        Rp {{ number_format($stats['cost_or_revenue'], 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- ================= SEARCH & TABS FILTER SECTION ================= -->
        <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 shadow-md space-y-4">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <!-- Search bar -->
                <div class="relative w-full md:max-w-md">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" x-model="searchQuery"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-500 outline-none text-sm transition"
                        placeholder="Cari laptop, nomor seri, atau keluhan...">
                </div>

                <!-- Active tab information -->
                <div class="text-xs text-slate-400">
                    Menampilkan <span class="font-bold text-white" x-text="filteredServices.length"></span> dari <span class="font-bold text-white" x-text="services.length"></span> antrean
                </div>
            </div>

            <!-- Tab Filters -->
            <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-800/50">
                <button type="button" @click="activeTab = 'all'"
                    :class="activeTab === 'all' ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-slate-900/60 text-slate-400 border-slate-800 hover:text-white'"
                    class="px-4 py-2 text-xs font-semibold rounded-xl border transition cursor-pointer flex items-center space-x-1.5">
                    <span>Semua</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-950 text-slate-400" x-text="services.length"></span>
                </button>

                <button type="button" @click="activeTab = 'pending'"
                    :class="activeTab === 'pending' ? 'bg-amber-500/20 text-amber-400 border-amber-500/30' : 'bg-slate-900/60 text-slate-400 border-slate-800 hover:text-white'"
                    class="px-4 py-2 text-xs font-semibold rounded-xl border transition cursor-pointer flex items-center space-x-1.5">
                    <span>Pending</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-950 text-amber-400" x-text="services.filter(s => s.status === 'pending').length"></span>
                </button>

                <button type="button" @click="activeTab = 'proses'"
                    :class="activeTab === 'proses' ? 'bg-blue-500/20 text-blue-400 border-blue-500/30' : 'bg-slate-900/60 text-slate-400 border-slate-800 hover:text-white'"
                    class="px-4 py-2 text-xs font-semibold rounded-xl border transition cursor-pointer flex items-center space-x-1.5">
                    <span>Proses</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-950 text-blue-400" x-text="services.filter(s => s.status === 'proses').length"></span>
                </button>

                <button type="button" @click="activeTab = 'selesai'"
                    :class="activeTab === 'selesai' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-slate-900/60 text-slate-400 border-slate-800 hover:text-white'"
                    class="px-4 py-2 text-xs font-semibold rounded-xl border transition cursor-pointer flex items-center space-x-1.5">
                    <span>Selesai</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-950 text-emerald-400" x-text="services.filter(s => s.status === 'selesai').length"></span>
                </button>

                <button type="button" @click="activeTab = 'diambil'"
                    :class="activeTab === 'diambil' ? 'bg-slate-800 text-slate-300 border-slate-700' : 'bg-slate-900/60 text-slate-400 border-slate-800 hover:text-white'"
                    class="px-4 py-2 text-xs font-semibold rounded-xl border transition cursor-pointer flex items-center space-x-1.5">
                    <span>Diambil</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-950 text-slate-500" x-text="services.filter(s => s.status === 'diambil').length"></span>
                </button>
            </div>
        </div>

        @if(Auth::user()->role === 'customer')
        <!-- ================= CUSTOMER DASHBOARD LAYOUT ================= -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Service History List -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">Riwayat Servis Laptop Anda</h2>
                </div>

                <!-- Client-side filter container -->
                <div class="space-y-4">
                    <!-- Empty State inside Alpine -->
                    <div x-show="filteredServices.length === 0" class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-12 text-center" x-cloak>
                        <p class="text-slate-400 font-medium">Tidak menemukan data servis yang cocok.</p>
                    </div>

                    <!-- Card Loop -->
                    <template x-for="service in filteredServices" :key="service.id">
                        <div class="bg-slate-900/50 hover:bg-slate-900 border border-slate-850/70 rounded-xl p-5 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h3 class="text-base font-bold text-white" x-text="service.device_name"></h3>
                                        <span class="text-[11px] text-indigo-400 font-mono" x-text="'#'+service.id"></span>
                                    </div>
                                    <div class="text-xs text-slate-400 mt-1">S/N: <span x-text="service.serial_number" class="font-mono"></span></div>
                                </div>
                                <div>
                                    <template x-if="service.status === 'pending'">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 uppercase tracking-wide">Menunggu</span>
                                    </template>
                                    <template x-if="service.status === 'proses'">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 uppercase tracking-wide">Dikerjakan</span>
                                    </template>
                                    <template x-if="service.status === 'selesai'">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase tracking-wide">Selesai</span>
                                    </template>
                                    <template x-if="service.status === 'diambil'">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-800 text-slate-400 border border-slate-700/60 uppercase tracking-wide">Diambil</span>
                                    </template>
                                </div>
                            </div>

                            <!-- Mini Summary -->
                            <p class="text-xs text-slate-400 truncate mt-3" x-text="'Keluhan: ' + service.complaints"></p>

                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-800/40">
                                <span class="text-xs font-semibold text-slate-500" x-text="service.created_at"></span>
                                <button type="button" @click="openDetailModal(service)"
                                    class="px-3 py-1.5 text-xs font-bold rounded-lg text-indigo-400 bg-indigo-500/10 border border-indigo-500/25 hover:bg-indigo-600 hover:text-white transition cursor-pointer">
                                    Lihat Detail
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Registration Form -->
            <div id="daftar-servis" class="space-y-4">
                <h2 class="text-xl font-bold text-white">Daftarkan Laptop Baru</h2>
                <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-6 shadow-xl backdrop-blur-md">
                    <form action="{{ route('services.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="device_name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Merk / Tipe Laptop</label>
                            <input type="text" id="device_name" name="device_name" required value="{{ old('device_name') }}"
                                class="w-full px-4 py-2.5 mt-1 text-sm rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-600 outline-none transition"
                                placeholder="Contoh: Asus ZenBook 14">
                        </div>
                        <div>
                            <label for="serial_number" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Nomor Seri (S/N)</label>
                            <input type="text" id="serial_number" name="serial_number" required value="{{ old('serial_number') }}"
                                class="w-full px-4 py-2.5 mt-1 text-sm rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-600 outline-none transition"
                                placeholder="Contoh: SN209283182">
                        </div>
                        <div>
                            <label for="phone_number" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Nomor HP / Telepon</label>
                            <input type="tel" id="phone_number" name="phone_number" required value="{{ old('phone_number') }}"
                                class="w-full px-4 py-2.5 mt-1 text-sm rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-600 outline-none transition"
                                placeholder="Contoh: 081234567890">
                        </div>
                        <div>
                            <label for="complaints" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Detail Keluhan / Kerusakan</label>
                            <textarea id="complaints" name="complaints" rows="4" required
                                class="w-full px-4 py-2.5 mt-1 text-sm rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-600 outline-none resize-none transition"
                                placeholder="Jelaskan kondisi laptop Anda secara detail..."></textarea>
                        </div>
                        <button type="submit"
                            class="w-full py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 shadow-md transition cursor-pointer">
                            Ajukan Perbaikan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @else
        <!-- ================= ADMIN / TEKNISI DASHBOARD LAYOUT ================= -->
        <div class="space-y-4">
            <h2 class="text-xl font-bold text-white">Daftar Antrean Servis Pelanggan</h2>

            <!-- Empty State inside Alpine -->
            <div x-show="filteredServices.length === 0" class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-12 text-center" x-cloak>
                <p class="text-slate-400 font-medium">Tidak menemukan data servis yang cocok.</p>
            </div>

            <!-- Table Container -->
            <div x-show="filteredServices.length > 0" class="bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-2xl backdrop-blur-md">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-950/80 text-xs font-semibold tracking-wider text-slate-400 border-b border-slate-800/80">
                                <th class="py-4.5 px-6">Pelanggan</th>
                                <th class="py-4.5 px-6">Laptop / S/N</th>
                                <th class="py-4.5 px-6">Status</th>
                                <th class="py-4.5 px-6">Total Biaya</th>
                                <th class="py-4.5 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-sm">
                            <template x-for="service in filteredServices" :key="service.id">
                                <tr class="hover:bg-slate-850/40 transition">
                                    <!-- Customer -->
                                    <td class="py-4 px-6">
                                        <div class="font-semibold text-white" x-text="service.customer_name"></div>
                                        <div class="text-xs text-slate-400 mt-0.5" x-text="service.customer_email"></div>
                                    </td>
                                    <!-- Laptop -->
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-indigo-300" x-text="service.device_name"></div>
                                        <div class="text-xs font-mono text-slate-400 mt-0.5" x-text="'S/N: ' + service.serial_number"></div>
                                    </td>
                                    <!-- Status -->
                                    <td class="py-4 px-6">
                                        <template x-if="service.status === 'pending'">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">Pending</span>
                                        </template>
                                        <template x-if="service.status === 'proses'">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">Proses</span>
                                        </template>
                                        <template x-if="service.status === 'selesai'">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Selesai</span>
                                        </template>
                                        <template x-if="service.status === 'diambil'">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-800 text-slate-400 border border-slate-700/60">Diambil</span>
                                        </template>
                                    </td>
                                    <!-- Cost -->
                                    <td class="py-4 px-6 font-semibold text-slate-200" x-text="service.formatted_cost"></td>
                                    <!-- Actions -->
                                    <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                                        <!-- Detail Button -->
                                        <button type="button" @click="openDetailModal(service)"
                                            class="px-2.5 py-1.5 text-xs font-semibold rounded-lg text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-700/60 transition cursor-pointer">
                                            Detail
                                        </button>

                                        <!-- Edit Button -->
                                        <button type="button" @click="openEditModal(service, '/services/' + service.id)"
                                            class="px-2.5 py-1.5 text-xs font-semibold rounded-lg text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 hover:bg-indigo-500 hover:text-white transition cursor-pointer">
                                            Update
                                        </button>

                                        <!-- Delete Button -->
                                        <button type="button" @click="openDeleteModal(service.id, '/services/' + service.id)"
                                            class="px-2.5 py-1.5 text-xs font-semibold rounded-lg text-rose-400 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500 hover:text-white transition cursor-pointer">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- ================= ALPINE MODAL: DETAIL TICKET ================= -->
        <div class="fixed inset-0 z-50 overflow-y-auto" x-show="detailModalOpen" style="display: none;" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="detailModalOpen = false"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative bg-slate-900 border border-slate-800 rounded-2xl max-w-2xl w-full p-6 shadow-2xl"
                    x-show="detailModalOpen"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95">

                    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                        <h3 class="text-lg font-bold text-white">Detail Diagnosa Servis #<span x-text="activeService.id"></span></h3>
                        <button @click="detailModalOpen = false" class="text-slate-400 hover:text-slate-200 cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mt-4 space-y-6">
                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Laptop Specs -->
                            <div class="bg-slate-950/60 rounded-xl p-4 border border-slate-800/80 space-y-2">
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Informasi Perangkat</h4>
                                <div class="text-sm font-semibold text-white" x-text="activeService.device_name"></div>
                                <div class="text-xs text-slate-400">Nomor Seri: <span class="font-mono text-indigo-400" x-text="activeService.serial_number"></span></div>
                                <div class="text-xs text-slate-400">Diajukan: <span x-text="activeService.created_at"></span></div>
                            </div>

                            <!-- Customer Details -->
                            <div class="bg-slate-950/60 rounded-xl p-4 border border-slate-800/80 space-y-2">
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pemilik Laptop</h4>
                                <div class="text-sm font-semibold text-white" x-text="activeService.customer_name"></div>
                                <div class="text-xs text-slate-400">Email: <span x-text="activeService.customer_email" class="text-slate-300"></span></div>
                                <div class="text-xs text-slate-400">No. HP: <span x-text="activeService.phone_number || '-'" class="text-indigo-400 font-mono"></span></div>
                                <div class="text-xs text-slate-400">Role Pengguna: <span class="text-emerald-400 font-semibold">Customer</span></div>
                            </div>
                        </div>

                        <!-- Status and Cost -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-slate-950/60 rounded-xl p-4 border border-slate-800/80">
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status Pengerjaan</h4>
                                <template x-if="activeService.status === 'pending'">
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/25">MENUNGGU ANTREAN</span>
                                </template>
                                <template x-if="activeService.status === 'proses'">
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-400 border border-blue-500/25">SEDANG DIPROSES</span>
                                </template>
                                <template x-if="activeService.status === 'selesai'">
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/25">SELESAI PERBAIKAN</span>
                                </template>
                                <template x-if="activeService.status === 'diambil'">
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-300 border border-slate-700">SUDAH DIAMBIL</span>
                                </template>
                            </div>

                            <div class="bg-slate-950/60 rounded-xl p-4 border border-slate-800/80">
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Biaya Servis</h4>
                                <div class="text-lg font-bold text-indigo-400 mt-1" x-text="activeService.formatted_cost"></div>
                            </div>
                        </div>

                        <!-- Complaints -->
                        <div class="bg-slate-950/60 rounded-xl p-4 border border-slate-800/80">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Deskripsi Keluhan</h4>
                            <p class="text-sm text-slate-300 mt-2 leading-relaxed whitespace-pre-line" x-text="activeService.complaints"></p>
                        </div>

                        <!-- Notes -->
                        <div class="bg-slate-950/60 rounded-xl p-4 border border-slate-800/80">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan Diagnosa / Tindakan Teknisi</h4>
                            <p class="text-xs text-slate-300 mt-2 leading-relaxed whitespace-pre-line"
                                :class="activeService.technician_notes ? 'text-slate-300' : 'text-slate-550 italic'"
                                x-text="activeService.technician_notes || 'Belum ada diagnosa / tindakan yang dicatat oleh teknisi.'"></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-800">
                        <button type="button" @click="detailModalOpen = false"
                            class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700/80 border border-slate-700/60 transition cursor-pointer">
                            Tutup
                        </button>

                        <!-- Print Action -->
                        <button type="button" @click="printReceipt()"
                            class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md transition flex items-center cursor-pointer">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Cetak Resi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= ALPINE MODAL: UPDATE TICKET (ADMIN ONLY) ================= -->
        <div class="fixed inset-0 z-50 overflow-y-auto" x-show="editModalOpen" style="display: none;" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="editModalOpen = false"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative bg-slate-900 border border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl"
                    x-show="editModalOpen"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95">

                    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                        <h3 class="text-lg font-bold text-white">Update Status & Diagnosa #<span x-text="activeService.id"></span></h3>
                        <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-200 cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form :action="updateUrl" method="POST" class="space-y-4 mt-4">
                        @csrf
                        @method('PUT')

                        <div class="bg-slate-950/60 rounded-xl p-3 border border-slate-800/80 text-xs space-y-1">
                            <div><span class="text-slate-500 font-semibold">Nama:</span> <span class="text-slate-300 font-semibold" x-text="activeService.customer_name"></span></div>
                            <div><span class="text-slate-500 font-semibold">Laptop:</span> <span class="text-slate-300 font-semibold" x-text="activeService.device_name"></span></div>
                            <div><span class="text-slate-500 font-semibold">S/N:</span> <span class="text-indigo-400 font-mono" x-text="activeService.serial_number"></span></div>
                        </div>

                        <div>
                            <label for="modal_status" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Status Perbaikan</label>
                            <select id="modal_status" name="status" x-model="activeService.status" required
                                class="w-full px-4 py-2.5 mt-1 rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white outline-none cursor-pointer">
                                <option value="pending">Pending (Menunggu Antrean)</option>
                                <option value="proses">Proses (Sedang Dikerjakan)</option>
                                <option value="selesai">Selesai (Siap Diambil)</option>
                                <option value="diambil">Diambil (Sudah Diambil Pelanggan)</option>
                            </select>
                        </div>

                        <div>
                            <label for="modal_cost" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Biaya Servis (Rp)</label>
                            <input type="number" id="modal_cost" name="total_cost" x-model="activeService.total_cost" min="0" step="any"
                                class="w-full px-4 py-2.5 mt-1 rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white outline-none"
                                placeholder="Estimasi biaya perbaikan...">
                        </div>

                        <div>
                            <label for="modal_notes" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Catatan Diagnosa / Tindakan</label>
                            <textarea id="modal_notes" name="technician_notes" x-model="activeService.technician_notes" rows="3"
                                class="w-full px-4 py-2.5 mt-1 rounded-xl bg-slate-950/60 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white outline-none resize-none"
                                placeholder="Sebutkan hasil cek kerusakan atau penggantian sparepart..."></textarea>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-800">
                            <button type="button" @click="editModalOpen = false"
                                class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700/80 border border-slate-700/60 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md transition cursor-pointer">
                                Simpan Diagnosa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================= ALPINE MODAL: DELETE TICKET (ADMIN ONLY) ================= -->
        <div class="fixed inset-0 z-50 overflow-y-auto" x-show="deleteModalOpen" style="display: none;" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="deleteModalOpen = false"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative bg-slate-900 border border-slate-850 rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center"
                    x-show="deleteModalOpen"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95">

                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-500/10 text-rose-500 mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>

                    <h3 class="text-lg font-bold text-white">Hapus Data Servis?</h3>
                    <p class="text-xs text-slate-400 mt-2">
                        Tindakan ini tidak bisa dibatalkan. Data permohonan servis laptop akan dihapus secara permanen dari server.
                    </p>

                    <div class="flex items-center justify-center space-x-3 mt-6">
                        <button type="button" @click="deleteModalOpen = false"
                            class="w-full px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700/80 border border-slate-700/60 transition cursor-pointer">
                            Batal
                        </button>
                        <form :action="deleteUrl" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 shadow-md transition cursor-pointer">
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ================= PRINT OUT RECEIPT / INVOICE SECTION (PRINT ONLY) ================= -->
    <div id="print-receipt-section" class="hidden text-black p-8 font-mono bg-white leading-normal">
        <div class="text-center border-b-2 border-black pb-4">
            <h1 class="text-xl font-bold uppercase">Tanda Terima & Kuitansi Servis Laptop</h1>
            <p class="text-xs mt-1">E-Service Hub &bull; Jl. Raya Kampus No. 123 &bull; Telp: 0812-3456-7890</p>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-6 text-xs border-b border-black pb-4">
            <div>
                <p class="font-bold">INVOICE/TIKET:</p>
                <p class="mt-1 font-semibold" x-text="'ID Transaksi: #' + activeService.id"></p>
                <p x-text="'Tanggal Masuk: ' + activeService.created_at"></p>
            </div>
            <div class="text-right">
                <p class="font-bold">PELANGGAN:</p>
                <p class="mt-1 font-semibold" x-text="activeService.customer_name"></p>
                <p x-text="activeService.customer_email"></p>
            </div>
        </div>

        <div class="mt-6 text-xs space-y-4">
            <div>
                <span class="font-bold block border-b border-black pb-1">IDENTITAS PERANGKAT:</span>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <div>Tipe Laptop: <span class="font-semibold" x-text="activeService.device_name"></span></div>
                    <div>S/N Perangkat: <span class="font-semibold" x-text="activeService.serial_number"></span></div>
                </div>
            </div>

            <div>
                <span class="font-bold block border-b border-black pb-1">KELUHAN / LAPORAN KERUSAKAN:</span>
                <p class="mt-2 italic whitespace-pre-line" x-text="activeService.complaints"></p>
            </div>

            <div>
                <span class="font-bold block border-b border-black pb-1">DIAGNOSA & TINDAKAN TEKNISI:</span>
                <p class="mt-2 whitespace-pre-line" x-text="activeService.technician_notes || '-'"></p>
            </div>

            <div class="pt-4 border-t border-black text-right">
                <span class="text-xs font-bold uppercase mr-4">Total Biaya Perbaikan:</span>
                <span class="text-sm font-extrabold" x-text="activeService.formatted_cost"></span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 mt-12 text-center text-xs">
            <div>
                <p>Teknisi Pemeriksa,</p>
                <div class="h-16"></div>
                <p class="border-t border-black w-32 mx-auto pt-1 font-bold">Admin Teknisi</p>
            </div>
            <div>
                <p>Pemilik Laptop,</p>
                <div class="h-16"></div>
                <p class="border-t border-black w-32 mx-auto pt-1 font-bold" x-text="activeService.customer_name"></p>
            </div>
        </div>

        <div class="text-center text-[10px] mt-12 text-slate-500 border-t border-dashed border-gray-400 pt-4">
            <p>Terima kasih atas kepercayaan Anda mempercayakan perbaikan laptop pada kami.</p>
            <p class="mt-1">Resi ini dicetak otomatis secara elektronik dan sah sebagai tanda bukti servis.</p>
        </div>
    </div>
</x-layouts.app>