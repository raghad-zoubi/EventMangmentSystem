<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    public $table = "sub_categories";

    public $fillable = [
        'name','id','category_id',
    ];

    public $primaryKey = 'id';
    public $timestamps = true;

    protected $hidden = [
        'updated_at', 'created_at',

    ];


    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class,'subCategory_id');
    }
}
