<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use App\Services\FCMService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function sendNotificationrToUser($fcmToken,$orderId)
    {
        $order = Order::where('id', $orderId)->get()->first();
        $rr=Notification::where('created_at',$order->updated_at)->get()->first();
        // dd($rr);
        $t=json_decode($rr->data);

        FCMService::send(
            $fcmToken,
            [
                'title' => $t->title,
                'body'=>$t->body,
            ]
        );

    }
    public function sendNotificationrToAdmin($fcmToken,$mesId)
    {
        $mes = Message::where('id', $mesId)->get()->first();
        $rr=Notification::where('created_at',$mes->updated_at)->get()->first();
        $t=json_decode($rr->data);
        //dd($t->title);
        FCMService::send(
            $fcmToken,
            [
                'title' => $t->title,
                'body' => $rr->body,
            ]
        );

    }
    public function fcmToken(Request $request){

        $user = User::find(auth()->id());
        $user->update(['fcm_token'=>$request['fcm_token']]);

        return response()->json('fcm updated successfully',200);

    }
    public function listNotifications()
    {
        $notifications = Notification::where('notifiable_id', auth()->id())->orderBy('created_at', 'DESC')->get();
        foreach ($notifications as $notification) {
            $t = json_decode($notification->data);
            $notification['Data'] = $t;
            $notification->makehidden('data', 'type','notifiable_type','notifiable_id','read_at')->get();


        }
        return $notifications;
    }
}
