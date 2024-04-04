<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    public $table = "messages";

    public $primaryKey = 'id';



    public $fillable = [

        'body', 'status', 'conversation_id', 'user_id','created_at','updated_at',
    ];

    public $timestamps = true;

    protected $hidden = [
        'updated_at'

    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }


}
