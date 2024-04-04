<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DashboardController extends Controller
{
    public function indexAccept()
    {
        $dash = Admin::where('role', 2)
            ->select('id', 'name', 'url_img', 'created_at')
            ->get();
        return response()->json($dash, Response::HTTP_OK);
    }

    public function index()
    {      $dash = Admin::where('role', 0)
        ->select('name', 'url_img', 'id')->get();

        foreach ($dash as $company){
            $company['comp_rate'] = (new RatingController)->operationCompany($company->id);
        }

        return response()->json($dash, Response::HTTP_OK);
    }

    public function show($idAdmin)
    {

        $det = Admin::where('id', $idAdmin)
            ->select('id', 'description', 'replay_speed', 'delivery_speed', 'user_id',
                'name', 'url_img', 'created_at')
            ->with(['user' => function ($query) {
                $query->select( 'id','email');
            }])
            ->get();
     //   $det['comp_rate'] = (new RatingController)->operationCompany($idAdmin->id);
        return response()->json($det);

    }
    public function destroy($idAdmin)
    {

        $admin = Admin::find($idAdmin);
        $admin->delete();
        return response()->json(null, 204);

    }


    public function admission(Request $request, $IdAdmin)
    {

        $dash = Admin::where('id', $IdAdmin);


        if ($request->role == 2) {
            $dash->update([
                'role' => $request->role,
            ]);
            $response = 'accept';

        }

        if ($request->role == 4) {
            $dash->delete();
            $response = 'refuse';
        }

        return response()->json($response, 200);

    }
}
