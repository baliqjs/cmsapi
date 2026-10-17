<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Mengambil riwayat log aktivitas untuk Admin
     */
    public function index(Request $request)
    {
        $query = ActivityLog::latest();

        if ($request->has('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_nama', 'like', "%{$search}%")
                  ->orWhere('user_username', 'like', "%{$search}%");
            });
        }

        $logs = $query->take(100)->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil riwayat log aktivitas sistem',
            'data' => $logs,
        ], 200);
    }

    /**
     * Membersihkan seluruh riwayat log aktivitas
     */
    public function clear(Request $request)
    {
        ActivityLog::truncate();

        return response()->json([
            'success' => true,
            'message' => 'Seluruh catatan log aktivitas telah berhasil dibersihkan',
            'data' => null,
        ], 200);
    }
}
