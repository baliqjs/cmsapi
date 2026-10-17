<?php 
 
namespace App\Http\Controllers; 
 
use App\Models\User; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Hash; 
 
class UsersController extends Controller 
{ 
    public function index() 
    { 
        $users = User::all(); 
        return response()->json([ 
            'success' => true, 
            'message' => 'Berhasil mengambil daftar user', 
            'data' => $users, 
        ], 200); 
    } 
 
    public function store(Request $request) 
    { 
        $validated = $request->validate([ 
            'nama' => 'required|string|max:255', 
            'username' => 'required|string|max:100|unique:users,username', 
            'password' => 'required|string|min:6', 
            'level' => 'required|in:admin,user', 
        ]); 
 
        $user = User::create([ 
            'nama' => $validated['nama'], 
            'username' => $validated['username'], 
            'password' => Hash::make($validated['password']), 
            'level' => $validated['level'], 
        ]); 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'User berhasil ditambahkan', 
            'data' => $user, 
        ], 201); 
    } 
 
    public function show(string $id) 
    { 
        $user = User::find($id); 
 
        if (!$user) { 
            return response()->json([ 
                'success' => false, 
                'message' => 'User tidak ditemukan', 
            ], 404); 
        } 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Detail data user ditemukan', 
            'data' => $user, 
        ], 200); 
    } 

     
    public function me(Request $request) 
    { 
        return response()->json([ 
            'success' => true, 
            'message' => 'Data profil pengguna login', 
            'data' => $request->user(), 
        ], 200); 
    } 
 
    public function update(Request $request, string $id) 
    { 
        $user = User::find($id); 
 
        if (!$user) { 
            return response()->json([ 
                'success' => false, 
                'message' => 'User tidak ditemukan', 
            ], 404); 
        } 
 
        $validated = $request->validate([ 
            'nama' => 'sometimes|required|string|max:255', 
            'username' => 'sometimes|required|string|max:100|unique:users,username,' . $id . ',id_users', 
            'password' => 'sometimes|nullable|string|min:6', 
            'level' => 'sometimes|required|in:admin,user', 
        ]); 
 
        if (!empty($validated['password'])) { 
            $validated['password'] = Hash::make($validated['password']); 
        } else { 
            unset($validated['password']); 
        } 
 
        $user->update($validated); 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Data user berhasil diperbarui', 
            'data' => $user, 
        ], 200); 
    } 
 
    public function destroy(string $id) 
    { 
        $user = User::find($id); 
 
        if (!$user) { 
            return response()->json([ 
                'success' => false, 
                'message' => 'User tidak ditemukan', 
            ], 404); 
        } 
 
        $user->delete(); 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Data user berhasil dihapus', 
        ], 200); 
    } 
} 