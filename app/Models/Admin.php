<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method hasMany(string $class, string $string)
 * @method belongsTo(string $class, string $string)
 * @method static with(string $string)
 * @method static where(string $string, int $int)
 */
class Admin extends Model
{
    use HasFactory;

    protected $table = 'admins';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'url_img',
        'description',
        'replay_speed',
        'delivery_speed',
        'role',
        'admin_id',
        'user_id',
        'created_at'
    ];

//    protected $hidden = [
//        'updated_at', 'created_at',
//
//    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'admin_id');
    }
}
