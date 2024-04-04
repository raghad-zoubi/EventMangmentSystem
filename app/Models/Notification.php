<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    public $table = "notifications";

    public $fillable = [

        'id', 'type','notifiable','data','read_at'
    ];

    public $primaryKey = 'id';
    public $timestamps = true;

    protected $hidden = [
        'updated_at', 'created_at',

    ];
}
