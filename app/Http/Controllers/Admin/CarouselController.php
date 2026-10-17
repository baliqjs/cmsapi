<?php 
 
namespace App\Http\Controllers\Admin; 
 
use App\Http\Controllers\Controller; 
use App\Models\Carousel; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Storage; 
 
class CarouselController extends Controller 
{ 
    public function index() 
    { 
        $carousels = Carousel::latest()->get(); 
        return response()->json([ 
            'success' => true, 
            'message' => 'Berhasil mengambil data carousel', 
            'data' => $carousels, 
        ], 200); 
    } 
 
    public function store(Request $request) 
    { 
        $request->validate([ 
            'nama_carousel' => 'required|string|max:255', 
            'gambar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', 
        ]); 
 
        $path = $request->file('gambar')->store('carousel', 'public'); 
 
        $carousel = Carousel::create([ 
            'nama_carousel' => $request->nama_carousel, 
            'gambar' => $path, 
        ]); 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Berhasil menambahkan carousel', 
            'data' => $carousel, 
        ], 201); 
    } 
 
    public function show(string $id) 
    { 
        $carousel = Carousel::find($id); 
 
        if (!$carousel) { 
            return response()->json([ 
                'success' => false, 
                'message' => 'Carousel tidak ditemukan', 
            ], 404); 
        } 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Detail carousel ditemukan', 
            'data' => $carousel, 
        ], 200); 
    } 
 
    public function update(Request $request, string $id) 
    { 
        $carousel = Carousel::find($id); 
 
        if (!$carousel) { 
            return response()->json([ 
                'success' => false, 
                'message' => 'Carousel tidak ditemukan', 
            ], 404); 
        } 
 
        $request->validate([ 
            'nama_carousel' => 'sometimes|required|string|max:255', 
            'gambar' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048', 
        ]); 
 
        if ($request->has('nama_carousel')) { 
            $carousel->nama_carousel = $request->nama_carousel; 
        } 
 
        if ($request->hasFile('gambar')) { 
            if ($carousel->gambar && Storage::disk('public')->exists($carousel->gambar)) { 
                Storage::disk('public')->delete($carousel->gambar); 
            } 
            $carousel->gambar = $request->file('gambar')->store('carousel', 'public'); 
        } 
 
        $carousel->save(); 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Carousel berhasil diperbarui', 
            'data' => $carousel, 
        ], 200); 
    } 
 
    public function destroy(string $id) 
    { 
        $carousel = Carousel::find($id); 
 
        if (!$carousel) { 
            return response()->json([ 
                'success' => false, 
                'message' => 'Carousel tidak ditemukan', 
            ], 404); 
        } 
 
        if ($carousel->gambar && Storage::disk('public')->exists($carousel->gambar)) { 
            Storage::disk('public')->delete($carousel->gambar); 
        } 
 
        $carousel->delete(); 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Carousel berhasil dihapus', 
        ], 200); 
    } 
} 