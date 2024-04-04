<?php

namespace App\Http\Controllers;


use App\Models\Admin;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ConversationController extends Controller
{
//كلشي محاد ثات عندي  عند ال  ومرتبين
    public function indexUser()
    {

        $conversations = DB::table('conversations as c')
            ->join('users as u', function ($join) {
                $join->on('u.id', '=', 'c.sender_user_id')
                    ->orOn('u.id', '=', 'c.receiver_user_id')
                ;
            })
            ->join('messages as m', 'm.conversation_id', '=', 'c.id')
            ->select(
                'a.name',
                'a.url_img',
                'c.id',
                DB::raw('MAX(m.created_at) as date_latest_message'),
                DB::raw("COUNT(If(status=0,m.id,Null)) as count_unread_message "),
            )
            ->where('c.sender_user_id','!=', auth()->id())
            ->orWhere('c.receiver_user_id','!=', auth()->id())
            ->groupBy('c.id', 'a.name', 'a.url_img', 'm.conversation_id')//,'m.body' 'a.id','c.sender_user_id',
            ->orderBy('date_latest_message', 'DESC')
            ->get();


        return response()->json($conversations, 200);
    }

    public function indexCompany()
    {
        $conversation = DB::table('conversations as c',)
           // ->join('users as u', 'u.id', '=',DB::raw('c.sender_user_id'or 'c.receiver_user_id') )
            ->join('users as u', function ($join) {

                $join->on('u.id', '=', 'c.sender_user_id')
                    ->orOn('u.id', '=', 'c.receiver_user_id');
            })
            ->join('messages as m', 'm.conversation_id', '=', 'c.id')
            ->select(

                'c.receiver_user_id',
                'u.name',
                'u.id',
                DB::raw('MAX(m.created_at) as date_latest_message'),
                DB::raw("COUNT(If(status=0,m.id,Null)) as count_unread_message "),
            )
            ->where([['c.receiver_user_id', auth()->id()]])
            ->where([['c.sender_user_id','!=', auth()->id()]])
            ->orwhere([['c.receiver_user_id','!=', auth()->id()]])
            ->where([['c.sender_user_id', auth()->id()]])

          //  ->orwhere([['c.receiver_user_id', auth()->id()]])
     //  ->orwhere([['c.sender_user_id','!=', auth()->id()],['c.receiver_user_id','!=', auth()->id()]])
           //->orwhere([['c.sender_user_id','! auth()->id()]])
                //,['c.receiver_user_id', auth()->id()]])
           // ->Where([['c.receiver_user_id','!=', auth()->id()],['c.sender_user_id', auth()->id()]])
            ->groupBy('c.receiver_user_id', 'u.name', 'u.id')

            ->orderBy('date_latest_message', 'DESC')
            ->get();
        return response()->json($conversation, 200);

    }
    public function indexCus($IdUser)
    {
        $co=User::query()->where('id',$IdUser)->select('id','name')->get();
        return response()->json($co);
    }

    public function indexAd($IdAdmin)
    {
        $co = Admin::query()->where('id', $IdAdmin)->select('id', 'name','url_img')->get();
        return response()->json($co);
    }


   // انشاء محادثه بين تنين
    public function createit(Request $request, $Id)
    {
        $conv = Conversation::query()->
        where('sender_user_id', auth()->id())
            ->where('receiver_user_id', $Id)
            ->orwhere('receiver_user_id', $Id)
            ->where('receiver_user_id',  auth()->id())
        ;
        $check = $conv->exists();

        if (!$check) {

            $request->validate([
                'body' => ['required', 'min:1']
            ]);

            $conversation = Conversation::create([

                'sender_user_id' => auth()->id(),
                'receiver_user_id' => $Id
            ]);

            $message = Message::create([

                'body' => $request['body'],
                'user_id' => auth()->id(),
                'conversation_id' => $conversation->id,
                'status' => false,
            ]);

        } else {
            $id = $conv->get()->first()->id;
            return (new ConversationController())->show($id);
        }

        return response()->json($message, 200);
    }

//    public function store ( $Id)
//    {
//        $conv = Conversation::query()->
//        where('sender_user_id', auth()->id())
//            ->where('receiver_user_id', $Id)
//            ->orwhere('receiver_user_id', $Id)
//            ->where('receiver_user_id',  auth()->id())
//        ;
//        $check = $conv->exists();
//
//        if (!$check) {
//            $user= User::where('id', $Id)->get()->first();
//      //    $customer = Customer::where('id', $order->customer_id)->get()-
////dd($user);
//        } else {
//            $id = $conv->get()->first()->id;
//            return (new ConversationController())->show($id);
//        }
//
//        return response()->json($message, 200);
//    }
    public function store(Request $request, $Id)
    {  $conv = Conversation::query()->
    where('sender_user_id', auth()->id())
        ->where('receiver_user_id', $Id)
        ->orwhere('receiver_user_id', $Id)
        ->where('receiver_user_id',  auth()->id());
        $check = $conv->exists();

        if (!$check) {

            $request->validate([
                'body' => ['required', 'min:1']
            ]);

            $conversation = Conversation::create([

                'sender_user_id' => auth()->id(),
                'receiver_user_id' => $Id
            ]);

            $message = Message::create([

                'body' => $request['body'],
                'user_id' => auth()->id(),
                'conversation_id' => $conversation->id,
                'status' => false,
            ]);

        } else {
            $id = $conv->get()->first()->id;
            return (new ConversationController())->show($id);
        }

        return response()->json($message, 200);
    }

// عرض محادثه   من خلال id امحادثه
// صاحب الرساله 1
//مو صاحب الرساله 2
    public function show($IdConversation)
    {
        $conversation = Conversation::find($IdConversation);

        $messages = $conversation->messages()->orderBy('created_at')->get();

        // $resev = $conversation->receiverUser()->get()->first()->id;

        foreach ($messages as $message) {

            if ($message->user_id == auth()->id()) {

                $message['direction'] = 1;

            } else {
                if ($message->status == 0) {
                    $message->update(['status' => 1]);
                    $message->save();
                }
                $message['direction'] = 2;
            }
        }
//
//        if ($resev == auth()->id()) {
//            foreach ($messages as $message) {
//                if ($message->status == 0) {
//                    $message->update(['status' => true]);
//                    $message->save();
//                }
//            }
//        }

        return response()->json($messages, 200);
    }


// حذف محادثه   من خلال id امحادثه
    public function destroy($IdConversation)
    {

        $conversation = Conversation::find($IdConversation);

        if (!$conversation) {
            $response = ['message' => 'error'];
        }
        if ($conversation && $conversation->receiver_user_id == auth()->id() || $conversation->sender_user_id == auth()->id()) {

            $conversation->delete();
            $response = ['message' => "success"];

        }
        if ($conversation && $conversation->receiver_user_id != auth()->id() || $conversation->sender_user_id != auth()->id()) {
            $response = ['message' => 'unauthorized'];
        }
        return response()->json($response, 200);
    }

}
