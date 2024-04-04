<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YourEvent extends Model
{
    use HasFactory;
    public $table = "your_events";

    public $fillable = [

        'image', 'customer_id',
    ];

    public $primaryKey = 'id';
    public $timestamps = true;

    protected $hidden = [
        'updated_at', 'created_at',

    ];



    public function likes()
    {
        return $this->hasMany(Like::class,'yourEvent_id');
    }
}
