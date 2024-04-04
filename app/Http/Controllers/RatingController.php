<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }


    public function store(Request $request, $id)
    {
        $customer = Customer::where('user_id', auth()->id())->get('id')->first();
        $rates = Rating::query()->where('customer_id', $customer->id)
            ->where('service_id', $id)->exists();

        if (!$rates) {
            for ($i = 1; $i <= 5; $i++) {
                if ($request->stars == $i) {
                    $s = Rating::create([
                        'stars' => $request->stars,
                        'service_id' => $id,
                        'customer_id' => $customer->id
                    ]);
                    return response()->json('you are rated success', 200);
                }
            }

        } else {
            return response()->json(' you are rated', 200);
        }
    }

    public function operationService($id)
    {
        $rrs = Rating::query()->where('service_id', $id)->get();
        $avg = round($rrs->average('stars'));
        return $avg;
    }

    public function operationCompany($id)
    {


        $d = DB::table('services as s')
            ->join('ratings as r', 'r.service_id', '=', 's.id')
            ->selectRaw('round(SUM(r.stars)/COUNT(distinct(r.service_id))) as compRate')
            ->where('s.admin_id', '=', $id)
            ->groupBy('s.admin_id')
            ->get();

        return $d ;

    }

}
