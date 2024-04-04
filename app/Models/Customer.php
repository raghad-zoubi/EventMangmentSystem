<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $string1)
 * @method belongsTo(string $class, string $string)
 * @method hasMany(string $class, string $string)
 */
class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'role',
    ];

    protected $hidden = [
        'updated_at', 'created_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function favoraits()
    {
        return $this->hasMany(Favorait::class, 'customer_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'customer_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'customer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function rating()
    {
        return $this->hasMany(Rating::class, 'customer_id');
    }
}
