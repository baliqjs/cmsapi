<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_from_flutter_records_flutter_platform(): void
    {
        $user = User::create([
            'username' => 'flutter_user',
            'nama' => 'Flutter User',
            'password' => Hash::make('secret123'),
            'level' => 'user',
        ]);

        $response = $this->withHeaders([
            'X-Platform' => 'flutter',
            'User-Agent' => 'Dart/3.5 (dart:io)',
        ])->postJson('/api/login', [
            'username' => 'flutter_user',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200);

        $log = ActivityLog::where('action', 'LOGIN')->latest()->first();
        $this->assertNotNull($log);
        $this->assertEquals('127.0.0.1', $log->ip_address);
        $this->assertEquals('Login Flutter: IP 127.0.0.1', $log->description);
    }

    public function test_login_from_website_records_website_platform(): void
    {
        $user = User::create([
            'username' => 'web_admin',
            'nama' => 'Web Admin',
            'password' => Hash::make('secret123'),
            'level' => 'admin',
        ]);

        $response = $this->withHeaders([
            'X-Platform' => 'website',
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ])->postJson('/api/login', [
            'username' => 'web_admin',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200);

        $log = ActivityLog::where('action', 'LOGIN')->latest()->first();
        $this->assertNotNull($log);
        $this->assertEquals('127.0.0.1', $log->ip_address);
        $this->assertEquals('Login Website: IP 127.0.0.1', $log->description);
    }
}
