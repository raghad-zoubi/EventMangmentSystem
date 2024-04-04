<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password', 'fcm_token'
    ];


    protected $hidden = [
        'password',
        'remember_token',
        'updated_at', 'created_at', 'email_verified_at'

    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id');
    }

    public function superAdmin()
    {
        return $this->hasOne(SuperAdmin::class, 'user_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'user_id');
    }

    public function pushNotification($title,$body,$message){

        $token = $this->fcm_token;


        if($token == null) {
            return 0;}

        $data['notification']['title']= $title;
        $data['notification']['body']= $body;
        $data['notification']['sound']= true;
        $data['priority']= 'normal';
        $data['data']['click_action'] = 'FLUTTER_NOTIFICATION_CLICK';
        $data['data']['message']=$message;
        $data['to'] = $token;


        $http = new \GuzzleHttp\Client(['headers'=>[
            'Centent-Type'=>'application/json',
            'Authorization'=>'key=AAAAuWiet7w:APA91bFMtMwvQJHHYe7VBzAMCy5wBRqRDyAXmnooA2Tpn2X0Tap9_o5WWvTuceJAsHDehnEWA2CZHpQ6jF65jg0sfn3mnfIRsk87lz0CeC4eNBh482pUkFrH_mCoEpWualUyvderE8Za'

        ]]);
        try {
            $response = $http->post('https://fcm.googleapis.com/fcm/send', [ 'json' =>
                $data
            ]);
            return $response->getBody();
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            // return $e->getCode();
            if ($e->getCode() === 400) {
                return response()->json(['ok'=>'0', 'erro'=> 'Invalid Request.'], $e->getCode());
            } else if ($e->getCode() === 401) {
                return response()->json('Your credentials are incorrect. Please try again', $e->getCode());
            }
            return response()->json('Something went wrong on the server.', $e->getCode());
        }

    }

}
