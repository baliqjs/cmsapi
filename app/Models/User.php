<?php 
 
namespace App\Models; 
 
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable; 
use Laravel\Sanctum\HasApiTokens; 
 
class User extends Authenticatable 
{ 
    use HasApiTokens, HasFactory, Notifiable; 
 
    protected $table = 'users'; 
    protected $primaryKey = 'id_users'; 
 
    protected $fillable = [ 
        'nama', 
        'username', 
        'password', 
        'level', 
    ]; 
 
    protected $hidden = [ 
        'password', 
        'remember_token', 
    ]; 
 
    protected function casts(): array 
    { 
        return [ 
            'password' => 'hashed', 
        ]; 
    } 
 
    public function konten() 
    { 
        return $this->hasMany(Konten::class, 'id_users', 'id_users'); 
    } 
} 