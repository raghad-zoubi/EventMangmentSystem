<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $customer_id)
 */
class Service extends Model
{
    use HasFactory;

    public $table = "services";

    public $fillable = [
        'name', 'price', 'available_time',
        'location', 'description',
        'admin_id', 'discount_percentage', 'date', 'subCategory_id','type',
        'color','size','capcity'
    ];

    public $primaryKey = 'id';
    public $timestamps = true;

    protected $hidden = [
        'updated_at', 'created_at',

    ];


    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'subCategory_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'service_id');
    }

    public function favoraits()
    {
        return $this->hasMany(Favorait::class, 'service_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'service_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'service_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }


    public function orders()
    {
        return $this->hasMany(Order::class, 'service_id');
    }

    public function service()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    //public $withCount = ['comments'];

}
