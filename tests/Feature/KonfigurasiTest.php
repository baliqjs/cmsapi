<?php

namespace Tests\Feature;

use App\Models\Konfigurasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KonfigurasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_public_konfigurasi(): void
    {
        $response = $this->getJson('/api/konfigurasi');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id_konfigurasi',
                    'judul_website',
                    'logo',
                    'profil_website',
                    'instagram',
                    'alamat',
                    'no_wa',
                ],
            ]);
    }

    public function test_authenticated_user_can_upload_logo_and_update_konfigurasi(): void
    {
        Storage::fake('public');

        $user = User::create([
            'username' => 'admin_test',
            'nama' => 'Admin Test',
            'password' => bcrypt('password'),
            'level' => 'admin',
        ]);

        $file = UploadedFile::fake()->image('custom_logo.png', 200, 200);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/konfigurasi', [
            'judul_website' => 'Portal Berita Baru',
            'profil_website' => 'Deskripsi baru',
            'instagram' => '@portalbaru',
            'alamat' => 'Jakarta Barat',
            'no_wa' => '089999999999',
            'logo' => $file,
        ]);

        $response->assertStatus(200);

        $konfigurasi = Konfigurasi::first();
        $this->assertNotNull($konfigurasi->logo);
        $this->assertEquals('Portal Berita Baru', $konfigurasi->judul_website);
        Storage::disk('public')->assertExists($konfigurasi->logo);

        // Test remove logo
        $removeResponse = $this->actingAs($user, 'sanctum')->postJson('/api/konfigurasi', [
            'remove_logo' => true,
        ]);

        $removeResponse->assertStatus(200);
        $konfigurasi->refresh();
        $this->assertNull($konfigurasi->logo);
    }
}
