<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Like;


class LikeController extends Controller
{

    public function store($IdEvent)
    {
        $customer = Customer::where('user_id', auth()->id())->get('id')->first();

        $like = Like::query()->where('customer_id', $customer->id)
            ->where('service_id', $IdEvent)->exists();

        if ($like) {
            Like::query()->where('customer_id', $customer->id)->where('yourEvent_id', $IdEvent)->delete();
            return response()->json('The delete is removed');

        } else {
            Like::query()->create([
                'is_like' => true,
                'service_id' => $IdEvent,
                'customer_id' => $customer->id,

            ]);
        }
        return response()->json('The like add success', 200);
    }
}

