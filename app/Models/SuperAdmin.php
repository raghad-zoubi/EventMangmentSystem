<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    use HasFactory;

    protected $table = 'super_admins';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'role',
        'user_id',
    ];

    protected $hidden = [
        'updated_at', 'created_at',

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
