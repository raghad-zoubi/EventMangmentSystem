<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $table = 'images';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'url_image', 'service_id'
    ];

    protected $hidden = [
        'updated_at', 'created_at',

    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
