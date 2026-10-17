<?php 
 
namespace App\Http\Controllers; 
 
use App\Models\Konfigurasi; 
use Illuminate\Http\Request; 
 
class KonfigurasiController extends Controller 
{ 
    public function show() 
    { 
        $konfigurasi = Konfigurasi::first(); 
 
        if (!$konfigurasi) { 
            $konfigurasi = Konfigurasi::create([ 
                'judul_website' => 'CMS Laravel', 
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
 
        if (!$konfigurasi) { 
            $konfigurasi = new Konfigurasi(); 
        } 
 
        $validated = $request->validate([ 
            'judul_website' => 'sometimes|required|string|max:100', 
            'profil_website' => 'sometimes|required|string', 
            'instagram' => 'sometimes|required|string|max:100', 
            'alamat' => 'sometimes|required|string|max:100', 
            'no_wa' => 'sometimes|required|string|max:100', 
        ]); 
 
        $konfigurasi->fill($validated); 
        $konfigurasi->save(); 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Konfigurasi website berhasil diperbarui', 
            'data' => $konfigurasi, 
        ], 200); 
    } 
} 