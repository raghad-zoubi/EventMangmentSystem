<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    public $table = "likes";

    public $fillable = [

        'service_id', 'customer_id','is_like'
    ];

    public $primaryKey = 'id';
    public $timestamps = true;

    protected $hidden = [
        'updated_at', 'created_at',

    ];



    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function yourEvent()
    {
        return $this->belongsTo(YourEvent::class,'yourEvent_id');
    }
}
