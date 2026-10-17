<?php

namespace App\Http\Controllers;

use App\Models\Konten;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KontenController extends Controller
{
    public function index(Request $request)
    {
        $query = Konten::with(['kategori', 'user'])->latest();

        // Status filter: jika request mengirimkan parameter status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('id_kategori')) {
            $query->where('id_kategori', $request->id_kategori);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $konten = $query->paginate(15);

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
            'status' => 'nullable|in:Published,Archived',
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $fotoPath = $request->file('foto')->store('konten', 'public');
        $status = $request->status ?: 'Published';

        $konten = Konten::create([
            'judul' => $request->judul,
            'keterangan' => $request->keterangan,
            'foto' => $fotoPath,
            'status' => $status,
            'slug' => Str::slug($request->judul),
            'id_kategori' => $request->id_kategori,
            'id_users' => $request->user()->id_users,
        ]);

        // Record activity log
        ActivityLog::record(
            $status === 'Published' ? 'PUBLISH_KONTEN' : 'CREATE_KONTEN',
            'Menambahkan artikel (' . $status . '): "' . $konten->judul . '"'
        );

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
            'status' => 'sometimes|nullable|in:Published,Archived',
            'foto' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $prevStatus = $konten->status;

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

        if ($request->has('status')) {
            $konten->status = $request->status;
        }

        if ($request->hasFile('foto')) {
            if ($konten->foto && Storage::disk('public')->exists($konten->foto)) {
                Storage::disk('public')->delete($konten->foto);
            }
            $konten->foto = $request->file('foto')->store('konten', 'public');
        }

        $konten->save();

        // Record activity log
        if ($request->has('status') && $request->status !== $prevStatus) {
            ActivityLog::record(
                $request->status === 'Archived' ? 'ARCHIVE_KONTEN' : 'PUBLISH_KONTEN',
                'Mengubah status artikel: "' . $konten->judul . '" menjadi ' . $request->status
            );
        } else {
            ActivityLog::record('UPDATE_KONTEN', 'Memperbarui data artikel: "' . $konten->judul . '"');
        }

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

        $judul = $konten->judul;

        if ($konten->foto && Storage::disk('public')->exists($konten->foto)) {
            Storage::disk('public')->delete($konten->foto);
        }

        $konten->delete();

        // Record activity log
        ActivityLog::record('DELETE_KONTEN', 'Menghapus artikel: "' . $judul . '"');

        return response()->json([
            'success' => true,
            'message' => 'Konten berhasil dihapus',
        ], 200);
    }
}
