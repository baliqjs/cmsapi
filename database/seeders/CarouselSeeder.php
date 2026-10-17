<?php

namespace Database\Seeders;

use App\Models\Carousel;
use Illuminate\Database\Seeder;

class CarouselSeeder extends Seeder
{
    /**
     * Seed the application's carousel.
     */
    public function run(): void
    {
        $carousels = [
            ['nama_carousel' => 'Selamat Datang', 'gambar' => 'carousel/slide-1.jpg'],
            ['nama_carousel' => 'Berita Terbaru', 'gambar' => 'carousel/slide-2.jpg'],
            ['nama_carousel' => 'Galeri Kegiatan', 'gambar' => 'carousel/slide-3.jpg'],
        ];

        foreach ($carousels as $carousel) {
            Carousel::updateOrCreate(
                ['nama_carousel' => $carousel['nama_carousel']],
                $carousel
            );
        }
    }
}
