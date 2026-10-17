<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
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
            'level' => 'nullable|in:admin,user,kontributor',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'level' => $validated['level'] ?? 'user',
        ]);

        ActivityLog::record('CREATE_USER', 'Pengguna baru terdaftar: '.$user->nama.' (@'.$user->username.')', $user);

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

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password tidak sesuai',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Record detailed activity log for login (Flutter vs Website)
        $platform = $this->resolvePlatform($request);
        $ip = $request->ip() ?: '127.0.0.1';

        ActivityLog::record(
            'LOGIN',
            "Login {$platform}: IP {$ip}",
            $user,
            $ip
        );

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

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data profil pengguna login',
            'data' => $request->user(),
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // Record activity log for logout
        if ($user) {
            $platform = $this->resolvePlatform($request);
            $ip = $request->ip() ?: '127.0.0.1';

            ActivityLog::record(
                'LOGOUT',
                "Logout {$platform}: IP {$ip}",
                $user,
                $ip
            );

            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil, token telah dicabut',
        ], 200);
    }

    private function resolvePlatform(Request $request): string
    {
        $platform = strtolower((string) ($request->header('X-Platform') ?: $request->input('platform', '')));
        $userAgent = strtolower((string) $request->header('User-Agent', ''));

        if (
            $platform === 'flutter' ||
            str_contains($platform, 'flutter') ||
            str_contains($userAgent, 'dart') ||
            str_contains($userAgent, 'flutter')
        ) {
            return 'Flutter';
        }

        if (
            $platform === 'website' ||
            $platform === 'react' ||
            $platform === 'next' ||
            $platform === 'web' ||
            str_contains($userAgent, 'mozilla') ||
            str_contains($userAgent, 'chrome') ||
            str_contains($userAgent, 'safari')
        ) {
            return 'Website';
        }

        return 'Website';
    }
}
