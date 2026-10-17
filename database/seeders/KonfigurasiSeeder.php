<?php

namespace Database\Seeders;

use App\Models\Konfigurasi;
use Illuminate\Database\Seeder;

class KonfigurasiSeeder extends Seeder
{
    /**
     * Seed the application's konfigurasi.
     */
    public function run(): void
    {
        Konfigurasi::updateOrCreate(
            ['id_konfigurasi' => 1],
            [
                'judul_website' => 'Datta CMS',
                'profil_website' => 'Website ini merupakan sistem manajemen konten berbasis Laravel yang digunakan untuk mengelola berita, artikel, pengumuman, dan galeri.',
                'instagram' => '@cmslaravel',
                'alamat' => 'Jl. Contoh No. 123, Jakarta',
                'no_wa' => '6281234567890',
            ]
        );
    }
}
