<?php

namespace App\Http\Controllers;

use App\Models\Carousel;
use App\Models\Kategori;
use App\Models\Konfigurasi;
use App\Models\Konten;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $konfigurasi = Konfigurasi::first();
        $carousel = Carousel::latest()->get();

        // Kategori beserta jumlah konten yang Published
        $kategori = Kategori::withCount(['konten' => function ($query) {
            $query->where('status', 'Published');
        }])->get();

        // Hanya konten yang berstatus Published yang tampil ke publik
        $recent_konten = Konten::with(['kategori', 'user'])
            ->where('status', 'Published')
            ->latest()
            ->take(6)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data beranda berhasil diambil',
            'data' => [
                'konfigurasi' => $konfigurasi,
                'carousel' => $carousel,
                'kategori' => $kategori,
                'recent_konten' => $recent_konten,
            ],
        ], 200);
    }
}
