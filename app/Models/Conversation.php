<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Conversation extends Model
{
    use HasFactory;

    public $table = "conversations";

    public $fillable = [

        'sender_user_id', 'receiver_user_id','created_at','updated_at','id'
    ];

    public $primaryKey = 'id';
    public $timestamps = true;

   

    public function senderUser()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function receiverUser()
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }
}
