<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{

    public function store(Request $request, $IdConversation)
    { //dd("kjk");
        $validator = Validator::make($request->all(), [
            'body' => ['required', 'string', 'min:1'],
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $message = Message::query()->create([
            'body' => $request->body,
            'status' => false,
            'conversation_id' => $IdConversation,
            'user_id' => auth()->id(),
        ]);
    //    $conv = Conversation::find($IdConversation);
    //    $receiver_user=$conv->receiver_user_id;
     //   $users=User::find($receiver_user);
      //  $user=$users->fcm_token;
        // dd([$receiver_user]);
     //   Notification::send($users, new Chat($message,$IdConversation));
   //     (new UserController)->sendNotificationrToAdmin($user,$message->id);
        //للاشعارات
         $conversation = $message->conversation;
        $user=User::find($conversation->user_id==auth()->id()?$conversation->receiver_user_id:$conversation->sender_user_id);
        $user->pushNotification('auth()->user()->name'.'send you massage',$message->body,$message);
        return response()->json($message, 201);
    }

// تعديل  عرساله محدده
    public function update(Request $request, $IdMessage)
    {

        $message = Message::find($IdMessage);
        if (auth()->id() == $message->user_id) {
            $validator = Validator::make($request->all(), [
                'body' => ['required', 'string', 'min:1'],
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $message->update([
                'body' => $request->body,
                'user_id' => auth()->id(),
            ]);
            $message->save();
        }
        return response()->json($message, 200);

    }


    public function destroy($IdMessage)
    {
        $message = Message::find($IdMessage);

        if (!Message::find($IdMessage)) {
            $response = ['message' => 'error'];
        }
        if (Message::find($IdMessage) && $message->user_id == auth()->id()) {

            $message->delete();
            $response = ['message' => "success"];
        }
        if (Message::find($IdMessage) && $message->user_id != auth()->id()) {
            $response = ['message' => 'unauthorized'];
        }
        return response()->json($response, 200);
    }
}
