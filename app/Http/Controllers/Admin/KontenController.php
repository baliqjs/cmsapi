<?php 
 
namespace App\Http\Controllers; 
 
use App\Models\Konten; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Str; 
 
class KontenController extends Controller 
{ 
    public function index(Request $request) 
    { 
        $query = Konten::with(['kategori', 'user'])->latest(); 
 
        if ($request->has('id_kategori')) { 
            $query->where('id_kategori', $request->id_kategori); 
        } 
 
        if ($request->has('search')) { 
            $search = $request->search; 
            $query->where('judul', 'like', "%{$search}%"); 
        } 
 
        $konten = $query->paginate(10); 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Berhasil mengambil daftar konten', 
            'data' => $konten, 
        ], 200); 
    } 
 
    public function store(Request $request) 
    { 
        $request->validate([ 
            'judul' => 'required|string|max:100|unique:konten,judul', 
            'keterangan' => 'required|string', 
            'id_kategori' => 'required|exists:kategori,id_kategori', 
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', 
        ]); 
 
        $fotoPath = $request->file('foto')->store('konten', 'public'); 
 
        $konten = Konten::create([ 
            'judul' => $request->judul, 
            'keterangan' => $request->keterangan, 
            'foto' => $fotoPath, 
            'slug' => Str::slug($request->judul), 
            'id_kategori' => $request->id_kategori, 
            'id_users' => $request->user()->id_users, 
        ]); 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Konten berhasil ditambahkan', 
            'data' => $konten->load(['kategori', 'user']), 
        ], 201); 
    } 
 
    public function show(string $id) 
    { 
        $konten = Konten::with(['kategori', 'user']) 
            ->where('id_konten', $id) 
            ->orWhere('slug', $id) 
            ->first(); 
 
        if (!$konten) { 
            return response()->json([ 
                'success' => false, 
                'message' => 'Konten tidak ditemukan', 
            ], 404); 
        } 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Detail konten ditemukan', 
            'data' => $konten, 
        ], 200); 
    } 
 
    public function update(Request $request, string $id) 
    { 
        $konten = Konten::find($id); 
 
        if (!$konten) { 
            return response()->json([ 
                'success' => false, 
                'message' => 'Konten tidak ditemukan', 
            ], 404); 
        } 
 
        $request->validate([ 
            'judul' => 'sometimes|required|string|max:100|unique:konten,judul,' . $id . ',id_konten', 
            'keterangan' => 'sometimes|required|string', 
            'id_kategori' => 'sometimes|required|exists:kategori,id_kategori', 
            'foto' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048', 
        ]); 
 
        if ($request->has('judul')) { 
            $konten->judul = $request->judul; 
            $konten->slug = Str::slug($request->judul); 
        } 
 
        if ($request->has('keterangan')) { 
            $konten->keterangan = $request->keterangan; 
        } 
 
        if ($request->has('id_kategori')) { 
            $konten->id_kategori = $request->id_kategori; 
        } 
 
        if ($request->hasFile('foto')) { 
            if ($konten->foto && Storage::disk('public')->exists($konten->foto)) { 
                Storage::disk('public')->delete($konten->foto); 
            } 
            $konten->foto = $request->file('foto')->store('konten', 'public'); 
        } 
 
        $konten->save(); 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Konten berhasil diperbarui', 
            'data' => $konten->load(['kategori', 'user']), 
        ], 200); 
    } 
 
    public function destroy(string $id) 
    { 
        $konten = Konten::find($id); 
 
        if (!$konten) { 
            return response()->json([ 
                'success' => false, 
                'message' => 'Konten tidak ditemukan', 
            ], 404); 
        } 
 
        if ($konten->foto && Storage::disk('public')->exists($konten->foto)) { 
            Storage::disk('public')->delete($konten->foto); 
        } 
 
        $konten->delete(); 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Konten berhasil dihapus', 
        ], 200); 
    } 
} 