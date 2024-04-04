<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method belongsTo(string $class, string $string)
 */
class Order extends Model
{
    use HasFactory;

    public $table = "orders";

    public $fillable = [
        'service_id',
        'customer_id',
        'quantity',
        'date',
        'time',
        'user_location',
        'size', 'notes',
        'status',
        'id',
        'updated_at',
    ];

    public $primaryKey = 'id';
    public $timestamps = true;

    protected $hidden = [
       'created_at',

    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
