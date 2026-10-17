<?php 
 
namespace App\Models; 
 
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model; 
 
class Carousel extends Model 
{ 
    use HasFactory; 
 
    protected $table = 'carousel'; 
    protected $primaryKey = 'id_carousel'; 
 
    protected $fillable = [ 
        'nama_carousel', 
        'gambar', 
    ]; 
 
    protected $appends = ['gambar_url']; 
 
    public function getGambarUrlAttribute() 
    { 
        if ($this->gambar) { 
            return asset('storage/' . $this->gambar); 
        } 
        return null; 
    } 
} 