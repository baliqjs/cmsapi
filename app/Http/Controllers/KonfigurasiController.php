<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Konfigurasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KonfigurasiController extends Controller
{
    public function show()
    {
        $konfigurasi = Konfigurasi::first();

        if (! $konfigurasi) {
            $konfigurasi = Konfigurasi::create([
                'judul_website' => 'CMS Laravel',
                'logo' => null,
                'profil_website' => 'Deskripsi profil website CMS',
                'instagram' => '@cmslaravel',
                'alamat' => 'Indonesia',
                'no_wa' => '081234567890',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil konfigurasi website',
            'data' => $konfigurasi,
        ], 200);
    }

    public function update(Request $request)
    {
        $konfigurasi = Konfigurasi::first();

        if (! $konfigurasi) {
            $konfigurasi = new Konfigurasi;
        }

        $request->validate([
            'judul_website' => 'sometimes|required|string|max:100',
            'profil_website' => 'sometimes|required|string',
            'instagram' => 'sometimes|required|string|max:100',
            'alamat' => 'sometimes|required|string|max:100',
            'no_wa' => 'sometimes|required|string|max:100',
            'logo' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'remove_logo' => 'sometimes|nullable',
        ]);

        $konfigurasi->fill($request->only([
            'judul_website',
            'profil_website',
            'instagram',
            'alamat',
            'no_wa',
        ]));

        // Handle penghapusan logo jika user memilih hapus logo
        $shouldRemoveLogo = $request->boolean('remove_logo') || $request->input('remove_logo') === '1' || $request->input('remove_logo') === 'true';
        if ($shouldRemoveLogo) {
            if ($konfigurasi->logo && Storage::disk('public')->exists($konfigurasi->logo)) {
                Storage::disk('public')->delete($konfigurasi->logo);
            }
            $konfigurasi->logo = null;
        }

        // Handle upload file logo baru
        if ($request->hasFile('logo')) {
            if ($konfigurasi->logo && Storage::disk('public')->exists($konfigurasi->logo)) {
                Storage::disk('public')->delete($konfigurasi->logo);
            }
            $konfigurasi->logo = $request->file('logo')->store('konfigurasi', 'public');
        }

        $konfigurasi->save();

        // Audit Trail Activity Log
        ActivityLog::record(
            'UPDATE_KONFIGURASI',
            'Memperbarui konfigurasi identitas & logo website'
        );

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi website berhasil diperbarui',
            'data' => $konfigurasi,
        ], 200);
    }
}
