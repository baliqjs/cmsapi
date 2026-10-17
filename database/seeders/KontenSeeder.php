<?php

namespace Database\Seeders;

use App\Models\Konten;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KontenSeeder extends Seeder
{
    /**
     * Seed the application's konten.
     */
    public function run(): void
    {
        $admin = User::where('level', 'admin')->first();
        $kategori = Kategori::pluck('id_kategori', 'nama_kategori');

        if (! $admin || $kategori->isEmpty()) {
            return;
        }

        $kontens = [
            [
                'judul' => 'Pembukaan Program Baru',
                'keterangan' => 'Kami dengan bangga mengumumkan pembukaan program baru untuk tahun ini.',
                'foto' => 'konten/pembukaan-program.jpg',
                'kategori' => 'Berita',
            ],
            [
                'judul' => 'Jadwal Pelatihan Bulan Ini',
                'keterangan' => 'Berikut jadwal pelatihan yang akan dilaksanakan pada bulan ini.',
                'foto' => 'konten/jadwal-pelatihan.jpg',
                'kategori' => 'Pengumuman',
            ],
            [
                'judul' => 'Tips Meningkatkan Produktivitas',
                'keterangan' => 'Beberapa tips yang dapat membantu Anda meningkatkan produktivitas kerja.',
                'foto' => 'konten/tips-produktivitas.jpg',
                'kategori' => 'Artikel',
            ],
        ];

        foreach ($kontens as $data) {
            $idKategori = $kategori[$data['kategori']] ?? $kategori->first();

            Konten::updateOrCreate(
                ['slug' => Str::slug($data['judul'])],
                [
                    'judul' => $data['judul'],
                    'keterangan' => $data['keterangan'],
                    'foto' => $data['foto'],
                    'slug' => Str::slug($data['judul']),
                    'id_kategori' => $idKategori,
                    'id_users' => $admin->id_users,
                ]
            );
        }
    }
}
