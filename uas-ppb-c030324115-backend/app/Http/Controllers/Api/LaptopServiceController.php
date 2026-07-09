<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaptopService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaptopServiceController extends Controller
{
    /**
     * Mengambil data servis untuk Dashboard Flutter (Format JSON)
     */
    public function index()
    {
        $user = Auth::user();
        
        // Mengambil antrean servis milik customer yang sedang login via token
        $services = $user->laptopServices()->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $services
        ], 200);
    }

    /**
     * Menyimpan pendaftaran servis baru dari aplikasi Flutter (Format JSON)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'device_name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255',
            'phone_number'  => 'required|string|max:255',
            'complaints' => 'required|string',
        ]);

        $service = LaptopService::create([
            'user_id' => $user->id,
            'device_name' => $request->device_name,
            'serial_number' => $request->serial_number,
            'phone_number'  => $request->phone_number,
            'complaints' => $request->complaints,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran servis berhasil diajukan lewat mobile!',
            'data' => $service
        ], 201);
    }
}