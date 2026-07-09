<?php

namespace App\Http\Controllers;

use App\Models\LaptopService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaptopServiceController extends Controller
{
    /**
     * Display the dashboard page based on user role.
     */
    public function index()
    {
        $user = Auth::user();
        $stats = [
            'total' => 0,
            'active' => 0,
            'completed' => 0,
            'cost_or_revenue' => 0,
        ];

        if ($user->role === 'admin') {
            // Admin sees all tickets along with customer info
            $services = LaptopService::with('user')->latest()->get();
            $stats['total'] = LaptopService::count();
            $stats['active'] = LaptopService::whereIn('status', ['pending', 'proses'])->count();
            $stats['completed'] = LaptopService::where('status', 'selesai')->count();
            $stats['cost_or_revenue'] = LaptopService::whereIn('status', ['selesai', 'diambil'])->sum('total_cost');
        } else {
            // Customer only sees their own tickets
            $services = $user->laptopServices()->latest()->get();
            $stats['total'] = $user->laptopServices()->count();
            $stats['active'] = $user->laptopServices()->whereIn('status', ['pending', 'proses'])->count();
            $stats['completed'] = $user->laptopServices()->where('status', 'selesai')->count();
            $stats['cost_or_revenue'] = $user->laptopServices()->whereIn('status', ['selesai', 'diambil'])->sum('total_cost');
        }

        return view('dashboard', compact('services', 'stats'));
    }

    /**
     * Store a newly created laptop service request (Customer only).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'customer') {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Customer.');
        }

        $request->validate([
            'device_name' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'phone_number'  => ['required', 'string', 'max:255'],
            'complaints' => ['required', 'string'],
        ]);

        LaptopService::create([
            'user_id' => $user->id,
            'device_name' => $request->device_name,
            'serial_number' => $request->serial_number,
            'phone_number'  => $request->phone_number,
            'complaints' => $request->complaints,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Pendaftaran servis laptop berhasil diajukan!');
    }

    /**
     * Update the laptop service record (Admin only).
     */
    public function update(Request $request, LaptopService $laptopService)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Admin/Teknisi.');
        }

        $request->validate([
            'status' => ['required', 'string', 'in:pending,proses,selesai,diambil'],
            'total_cost' => ['nullable', 'numeric', 'min:0'],
            'technician_notes' => ['nullable', 'string'],
        ]);

        $laptopService->update([
            'status' => $request->status,
            'total_cost' => $request->total_cost,
            'technician_notes' => $request->technician_notes,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Data servis laptop #' . $laptopService->id . ' berhasil diperbarui!');
    }

    /**
     * Remove the laptop service record (Admin only).
     */
    public function destroy(LaptopService $laptopService)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Admin/Teknisi.');
        }

        $laptopService->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Data servis laptop #' . $laptopService->id . ' berhasil dihapus!');
    }
}
