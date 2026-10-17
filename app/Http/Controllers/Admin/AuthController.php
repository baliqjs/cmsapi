<?php 
 
namespace App\Http\Controllers; 
 
use App\Http\Controllers\Controller;
use App\Models\User; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Hash; 
 
class AuthController extends Controller 
{ 
    public function register(Request $request) 
    { 
        $validated = $request->validate([ 
            'nama' => 'required|string|max:255', 
            'username' => 'required|string|max:100|unique:users,username', 
            'password' => 'required|string|min:6', 
            'level' => 'nullable|in:admin,user', 
        ]); 
 
        $user = User::create([ 
            'nama' => $validated['nama'], 
            'username' => $validated['username'], 
            'password' => Hash::make($validated['password']), 
            'level' => $validated['level'] ?? 'user', 
        ]); 
 
        $token = $user->createToken('auth_token')->plainTextToken; 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Registrasi user berhasil', 
            'data' => [ 
                'user' => $user, 
                'token' => $token, 
                'token_type' => 'Bearer', 
            ], 
        ], 201); 
    } 
 
    public function login(Request $request) 
    { 
        $request->validate([ 
            'username' => 'required|string', 
            'password' => 'required|string', 
        ]); 
 
        $user = User::where('username', $request->username)->first(); 
 
        if (!$user || !Hash::check($request->password, $user->password)) { 
            return response()->json([ 
                'success' => false, 
                'message' => 'Username atau password tidak sesuai', 
            ], 401); 
        } 
 
        $token = $user->createToken('auth_token')->plainTextToken; 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Login berhasil', 
            'data' => [ 
                'user' => $user, 
                'token' => $token, 
                'token_type' => 'Bearer', 
            ], 
        ], 200); 
    } 

 
    public function logout(Request $request) 
    { 
        $request->user()->currentAccessToken()->delete(); 
 
        return response()->json([ 
            'success' => true, 
            'message' => 'Logout berhasil, token telah dicabut', 
        ], 200); 
    } 
} 