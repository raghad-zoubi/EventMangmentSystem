<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Favorait;
use App\Models\Service;
use App\Models\User;
//use Illuminate\Http\Response;

//use App\Http\Requests\StoreChatRequest;
//use App\Http\Requests\UpdateChatRequest;


class FavoraitController extends Controller
{

    public function index() //user + foreach +image
    {//
        $customer = Customer::where('user_id', auth()->id())->get()->first();

        $services = Favorait::where('customer_id', $customer->id)->get();
        foreach ($services as $service) {
            $se = Service::where('id', $service->customer_id)->get()->first();

            $adm = Admin::with('services')->where('id', $se->admin_id)->get()->first();
            $se['company_name'] = $adm->name;

            $se->makehidden('categoryTwo_id', 'admin_id', 'description', 'location', 'price', 'available_time', 'discount', 'expration_date', 'name')->get();

            $service['service'] = $se;
            $service->makehidden('customer_id', 'service_id')->get();

        }
//dd($services);
        return response()->json($services);
    }

    public function store($id) //user
    {
        $customer = Customer::where('user_id', auth()->id())->get('id')->first();

        $favorait = Favorait::query()->where('customer_id', $customer->id)
            ->where('service_id', $id)->exists();
        if (!$favorait) {
            $f = Favorait::query()->create([
                'service_id' => $id,
                'customer_id' => $customer->id,
            ]);
            return response()->json($f);
        } else {
            Favorait::query()->where('customer_id', $customer->id)->where('service_id', $id)->delete();
            return response()->json('remove from favorait');
        }
    }

}
