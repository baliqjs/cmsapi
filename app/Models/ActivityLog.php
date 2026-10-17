<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'user_id',
        'user_nama',
        'user_username',
        'user_level',
        'action',
        'description',
        'ip_address',
    ];

    public static function record(string $action, string $description, ?User $user = null, ?string $ip = null): self
    {
        $user = $user ?: auth('sanctum')->user();

        return self::create([
            'user_id' => $user ? $user->id_users : null,
            'user_nama' => $user ? $user->nama : 'Guest',
            'user_username' => $user ? $user->username : 'guest',
            'user_level' => $user ? $user->level : 'Visitor',
            'action' => $action,
            'description' => $description,
            'ip_address' => $ip ?: request()->ip(),
        ]);
    }
}
