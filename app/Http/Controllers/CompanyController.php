<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Image;
use App\Models\Rating;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{

    public function index()//user rate
    {
        $companies = Admin::query()->select('name', 'url_img', 'id')->get();

        foreach ($companies as $company){
        $company['comp_rate'] = (new RatingController)->operationCompany($company->id);
        }

        return response()->json($companies, 200);
    }

    //______________________________________________________________________________________

    /*public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required', 'string', 'min:3',
            'url_img' => 'required',
            'description' => 'required', 'string',
            'replay_speed' => 'required',
            'delivery_speed' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $admin = Admin::where('user_id', auth()->id());

        $admin->update([

            'name' => $request->name,
            'url_img' => $request->file('url_img')->store('image'),
            'description' => $request->description,
            'replay_speed' => $request->replay_speed,
            'delivery_speed' => $request->delivery_speed,
        ]);


        return response()->json($admin->get());

    }*/

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required', 'string', 'min:3',
            'url_img' => 'required',
            'description',
            'replay_speed' => 'required',
            'delivery_speed' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

//        $id = User::with( 'admin')->where('user_id', auth()->id())->get();
//        $id->get('id');
//          $admin = Admin::find($id);
//        //$id=1;//3;
//       dd( $admin->id);

        // $id=2;
        $admin = Admin::where('user_id', auth()->id())->get()->first();

        if (Admin::find($admin->id) )
        {

            $admin-> name = $request->name;
            $admin-> description =$request->description;
            $admin->replay_speed= $request->replay_speed;
            $admin->  delivery_speed =$request->delivery_speed;
            if($request->hasfile('url_img'))
            {
                $file = $request->file('url_img');
                $extention = $file->getClientOriginalExtension();
                $filename = time().'.'.$extention;
                $file->move('uploads/events/', $filename);

                $admin->url_img =$filename;


            }
//            else
//                $admin->url_img ='';

//            $admin->update([
//                'name' => $name,
//                'url_img' => $url_img,
//                'description '=>$description,
//                'replay_speed' => $replay_speed,
//                'delivery_speed '=>$delivery_speed,
//            ]);
            $admin->save();

        }

        return response()->json([$admin, 200]);


    }

    //___________________________________________________________________________________________


    public function show() //admin
    {
        $admin = Admin::where('user_id', auth()->id())->get()->first();
        $company = Admin::find($admin->id);
        $company['comp_rate'] = (new RatingController)->operationCompany($admin->id);

        return response()->json($company);
    }

    //_________________________________________________________________________________________________

    public function showUser($IdCompany) //user
    {
        $company = Admin::where('id', $IdCompany)->select('id', 'name', 'url_img', 'description', 'replay_speed', 'delivery_speed')->get()->first();
        $company['comp_rate'] = (new RatingController)->operationCompany($company->id);

        return response()->json($company);
    }


    //_________________________________________________________________________________________________


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'replay_speed' => 'required',
            'delivery_speed' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $company = Admin::where('user_id', auth()->id());
        $company->update([
            'replay_speed' => $request->replay_speed,
            'delivery_speed' => $request->delivery_speed,
        ]);
        return response()->json('updated success', 200);
    }

    //_______________________________________________________________________

    //search admin
    public function search(Request $request)
    {
        $admin = Admin::where('user_id', auth()->id())->get()->first();

        if ($request->has('name')) {
            $data = Service::where('name', $request->input('name'))->where('admin_id', $admin->id)->get();
        }
        return response()->json($data, Response::HTTP_OK);
    }

    //______________________________________________________________________________________


    public function destroy($id)
    {

    }
}
