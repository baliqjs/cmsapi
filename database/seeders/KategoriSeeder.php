<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Seed the application's kategori.
     */
    public function run(): void
    {
        $kategoris = [
            'Berita',
            'Pengumuman',
            'Artikel',
            'Galeri',
            'Event',
        ];

        foreach ($kategoris as $nama) {
            Kategori::updateOrCreate(['nama_kategori' => $nama]);
        }
    }
}
