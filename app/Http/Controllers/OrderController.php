<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Notifications\Accept;
use App\Notifications\Refusal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function index() //user
    {

        $customer = Customer::where('user_id',auth()->id())->get()->first();
     // dd( );
        $MyOrder = DB::table('orders as o')
            ->join('services as s', 's.id', '=', 'o.service_id')
            ->select('o.id', 'o.date', 's.name', 'o.status')
            ->where('o.customer_id', '=', $customer->id)
            ->groupBy('o.date', 'o.id', 's.name', 'o.status')
            ->get();
        return response()->json($MyOrder);
    }

//_________________________________________________________________

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => ['numeric', 'min:1'],
            'date' => ['required', 'date'],
            'time' => ['required',],
            'user_location' => ['required', 'string'],

        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $customer = Customer::where('user_id', auth()->id())->get()->first();

        $order = Order::query()->create([
            'quantity' => $request->quantity,
            'date' => $request->date,
            'time' => $request->time,
            'user_location' => $request->user_location,
            'size' => $request->size,
            'notes' => $request->notes,
            'service_id' => $id,
            'customer_id' => $customer->id,
        ]);

        return response()->json($order,200);
    }
//______________________________________________________________

    public function status($orderId, Request $request)
    {    //$response="ll";
      // dd('jj');
        $order = Order::where('id', $orderId)->get()->first();
        //dd($order->customer_id);
       $customer = Customer::where('id', $order->customer_id)->get()->first();
        // dd($customer);
        $users = User::where('id', $customer->user_id)->get()->first();
        // dd($users);
        $or=Order::find($orderId);
        if(Order::find($orderId)){
            if ($request->status == '2') {
                $status = $request->status;
                $or->update([
                    'status' => $status,]);
                $or->save();
                $response = 'the order is accept';

          $user = User::find($users);
           $fcmToken=$users->fcm_token;
          // dd([$fcmToken]);
           Notification::send($users, new Accept([$fcmToken],$order));
                (new UserController)->sendNotificationrToUser($fcmToken,$orderId);
            }

            if ($request->status == '3') {
                $order->delete();
                $response = 'the order is refuse';

           $user = User::find($users);
          $fcmToken = $users->fcm_token;
          //  dd($users->fcm_token);
            Notification::send($users, new Refusal([$fcmToken], $order));
           //     (new UserController)->sendNotificationrToUser($fcmToken,$orderId);
            }
        }

        return response()->json($response);
    }
    //__________________________________________________

    //admin order all قيد الانتظار
    public function orderPending()
    {
        $admin = Admin::where('user_id', auth()->id())->get()->first();
        $pending = DB::table('orders as o')
            ->join('services as s', 's.id', '=', 'o.service_id')
            ->select('o.date', 's.name','o.id')
            ->where('s.admin_id', '=', $admin->id)
            ->where('o.status', '=', '1')
            ->groupBy('o.date', 's.name','o.id')
            ->orderByDesc('date')
            ->get();
        return response()->json($pending);

    }
//______________________________________________________________
    //admin order all مقبولة
    public function orderAccept()
    {
        $admin = Admin::where('user_id', auth()->id())->get()->first();
        $accept = DB::table('orders as o')
            ->join('services as s', 's.id', '=', 'o.service_id')
            ->select('o.date', 's.name','o.id')
            ->where('s.admin_id', '=', $admin->id)
            ->where('o.status', '=', '2')
            ->where('o.date', '>', now())
            ->groupBy('o.date', 's.name','o.id')
            ->orderByDesc('date')
            ->get();
        return response()->json($accept);

    }
//___________________________________________________________________
    //طلبات منفذة
    public function orderExecute()
    {
        $admin = Admin::where('user_id', auth()->id())->get()->first();
        $execute = DB::table('orders as o')
            ->join('services as s', 's.id', '=', 'o.service_id')
            ->select('o.date', 's.name','o.id')
            ->where('s.admin_id', '=', $admin->id)
            ->where('o.status', '=', '2')
            ->where('o.date', '<', now())
            ->groupBy('o.date', 's.name','o.id')
            ->orderByDesc('date')
            ->get();
        return response()->json($execute);

    }
//__________________________________________________________________________________

//admin & user
    public function show($IdOrder)//تفاصيل الطلب بس بدي  قيم المستخدم//
    {
        //$customer = Customer::where('user_id', auth()->id())->get('id')->first();

        $myorder = Order::with('service')->/*where('customer_id', $customer->id)->*/ where('id', $IdOrder)->get();

        return response()->json($myorder);

    }

//---------------------------------------------------


}
