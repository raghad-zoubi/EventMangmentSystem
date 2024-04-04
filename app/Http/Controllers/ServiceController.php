<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Image;
use App\Models\Service;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

function getdatabetween(string $string, string $start, string $end)
{

    $sp = strpos($string, $start)+strlen($start);
    $ep = strpos($string, $end)-strlen($start);
    $data = trim(substr($string, $sp, $ep));
    return trim($data);}

function getString($string, $from, $to) {
    $str = explode($from, $string);
    $str = explode($to, $str[1]);
    return $str[1];
}



class ServiceController extends Controller
{

    public function index($IdSubCategory)  //user
    {
        $services = Service::where('subCategory_id', $IdSubCategory)->get();

        foreach ($services as $service) {

            $adm = Admin::with('services')->where('id', $service->admin_id)->get()->first()->name;
            $service['company_name'] = $adm;
            $service['serv_rate'] = (new RatingController)->operationService($service->id);

            $service->makehidden('subCategory_id', 'admin_id', 'description', 'location', 'price', 'discount', 'expration_date')->get();
            $image = Image::where('service_id', $service->id)
                ->select('id', 'url_image')
                ->get()->first();

            $service['image'] = $image;
        }
        return response()->json($services, 200);
    }

//_______________________________________________________________________

    public function store(Request $request) //admin
    {
        $validator = Validator::make($request->all(), [
            'name' =>'string', 'min:3',
         
            'description' => 'string',
            'color' => 'string',
            'size' => 'string',
            'type' => 'string',
            'capcity' => 'string',
            'location' =>'string',
            'price' => 'numeric',
            'url_image' => 'required', 'file',
            'subCategory' => 'required',   'string',

        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        if (!$request->hasFile('url_image')) {
            return response()->json(['upload_file_not_found'], 400);
        }//
        $category = SubCategory::where('name', $request->subCategory)->get('id')->first();
      //  echo $category->id;
      //  dd($category);
        $admin = Admin::where('user_id', auth()->id())->get()->first();


        $service = Service::query()->create([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'price' => $request->price,
            'admin_id' => $admin->id,
            'type' => $request->type,
            'size' => $request->size,
            'color' => $request->color,
            'capcity' => $request->capcity,
            'subCategory_id' => $category->id,
        ]);



        $photoList = $request->file('url_image');
        foreach ($photoList as $photo) {

            $newPhoto = time().$photo->getClientOriginalName();

            $image = Image::query()->create([
                "url_image"=>'uploads/event/'.$newPhoto,
                "service_id"=>$service->id,
            ]);
            $photo->move('uploads/event',$newPhoto);
            $image->save();



        }
        return response()->json($service,201);
    }

    //___________________________________________________________________________


    public function show($id) //user
    {
        $services = Service::where('id', $id)->get()->first();

        if (($services['expration_date']) > (now()->format('Y-m-d'))) {
            $p = $services['discount'];
            $price = $services['price'];
            $p = (($p / 100) * $price);
            $services['sale'] = $price - $p;
        } else {
            $services->update([
                'discount' => 0,
            ]);
        }

        $adm = Admin::with('services')->where('id', $services->admin_id)->get()->first()->name;
        $services['company_name'] = $adm;
        $services['serv_rate'] = (new RatingController)->operationService($services->id);

        $image = Image::where('service_id', $services->id)
            ->select('id', 'url_image')
            ->get();
        $services['image'] = $image;


        $services->makehidden('admin_id', 'subCategory_id')->get();
        return response()->json($services, 200);

    }

    //__________________________________________________________________________________________


    public function update(Request $request, $id) //admin
    {
        $validator = validator::make($request->all(), [
            'name' =>'string', 'min:3',
            'url_image' => 'required', 'file',
            'description' => 'string',
            'color' => 'string',
            'size' => 'string',
            'type' => 'string',
            'capcity' => 'string',
            'location' =>'string',
            'price' => 'numeric',
            'subCategory' => 'string',

            //'SubCategory' => 'required', 'string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }
        $category = SubCategory::where('name', $request->SubCategory)->get('id')->first();


        $admin = Admin::where('user_id', auth()->id())->get()->first();

        $service = Service::find($id);

        if (!Service::find($id)) {

            $response = response()->json(['message' => 'error'], 404);
        }
        if (Service::find($id) && $service->admin_id !== $admin->id) {

            $response = response()->json(['message' => 'unauthorized'], 401);
        }
        if (Service::find($id) && $service->admin_id == $admin->id) {

            $service->update([
                'name' => $request->name,
                'description' => $request->description,
                'location' => $request->location,
                'price' => $request->price,
                'admin_id' => $admin->id,
                'type' => $request->type,
                'size' => $request->size,
                'color' => $request->color,
                'capcity' => $request->capcity,
                'SubCategory_id' => $category->id,


            ]);

            //      if ($request->hasFile('url_image')!=null)
            {

                $photoList = $request->file('url_image');
                foreach ($photoList as $photo) {

                    $newPhoto = time() . $photo->getClientOriginalName();

                    $image = Image::query()->create([
                        "url_image" => 'uploads/event/' . $newPhoto,
                        "service_id" => $service->id,
                    ]);
                    $photo->move('uploads/event', $newPhoto);
                    $image->save();

                }
            }
        }

        return response()->json($service, 200);
    }
    public function deletimg($string)
    {
        $st = Str::length($string);
        for ($i = 0; $i < $st; $i++) {

            $d = Image::where('id', $string[$i])->delete();

        }
        return \response()->json('kk');

    }

    //_________________________________________________________________________________________

    public function destroy($id) //admin
    {
        $admin = Admin::where('user_id', auth()->id())->get()->first();

        $service = Service::find($id);
        if (!Service::find($id)) {
            $response = ['message' => 'error'];
        }
        if (Service::find($id) && $service->admin_id == $admin->id) {

            $service->delete();
            $response = ['message' => "success"];

        }
        if (Service::find($id) && $service->admin_id !== $admin->id) {
            $response = ['message' => 'unauthorized'];
        }
        return response()->json($response, 200);
    }

//____________________________________________________________________________________

    public function storeOffer(Request $request, $id) //admin
    {
        $validator = Validator::make($request->all(), [
            'discount' => 'required', 'double',
            'expration_date' => 'required', 'date',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $s = Service::where('id', $id);
        $s->update([
            'discount' => $request->discount,
            'expration_date' => Carbon::parse($request->expration_date),
        ]);
        return response()->json('offer add success', 200);

    }

    //_____________________________________________________________________________

    public function indexOfferAdmin()  //admin
    {
        $admin = Admin::where('user_id', auth()->id())->get()->first();

        $services = Service::where([['admin_id', $admin->id], ['expration_date', '>', (now()->format('Y-m-d'))]])
            ->select('id', 'name', 'expration_date')
            ->get();

        foreach ($services as $service) {
            $image = Image::where('service_id', $service->id)
                ->select( 'url_image')
                ->get()->first();
            $service['serv_rate'] = (new RatingController)->operationService($service->id);
            $service['image'] = $image;

        }
//        $service = DB::table('images as im')
//            ->join('services as s', 'im.service_id', '=', 's.id')
//            ->select('s.id', 's.name', 's.expration_date','im.url_image')
//            ->where('s.admin_id', $admin->id)
//            ->where('s.expration_date', '>', (now()->format('Y-m-d')))
//            ->groupBy('s.id', 's.name', 's.expration_date','im.url_image')
//            ->get();

        return response()->json($services, 200);
    }

    //_________________________________________________________________________________

    public function indexOfferUser() //user
    {
        $services = Service::where('expration_date', '>=', (now()->format('Y-m-d')))
            ->select('id', 'name', 'expration_date')
            ->get();

        foreach ($services as $service) {
            $image = Image::where('service_id', $service->id)
                ->select('id', 'url_image')
                ->get()->first();
            //  $service['serv_rate'] = (new RatingController)->operationService($service->id);
            $service['image'] = $image;
        }

        return response()->json($services, 200);
    }

    //___________________________________________________________________________________________

    public function search(Request $request) //company name
    {
        $service = Service::query();

        if ($request->name) {
            $service->where('name', 'like', "%{$request->name}%")->orderBy('price')->get();
        }

        if ($request->location) {
            $service->where('location', 'like', "%{$request->location}%")->get();
        }
        //
        if ($request->companyName) {
            $service->where('name', 'like', "%{$request->companyName}%")->with('admins')->get();
        }

        return response()->json($service->get(), 200);
    }
    public function filter(Request $request)
    {
        $service = Service::query();
        foreach ($service as $r) {
            $r['serv_rate'] = (new RatingController)->operationService($r->id);
        }
        if ($request->Category_id) {
            $filter = DB::table('categories as c')
                ->join('sub_categories as s', 's.category_id', '=', 'c.id')
                ->join('services as se', 'se.subCategory_id', '=', 's.id')
                ->where('c.id', $request->Category_id);
        }

        if ($request->subCategory_id) {

            $filter = $service->where('subCategory_id', $request->subCategory_id);
        }

        if ($request->admin_id != -3) {
            $filter = $service->where('admin_id', $request->admin_id);
        }

        if ($request->from != -3 && $request->to!=0) {
            $filter = $service->whereBetween('price', array($request->from, $request->to));
        }

        if ($request->size!=0) {
            $filter = $service->where('size', $request->size);
        }
        if ($request->offer ==1) {
            $filter = $service->where('expration_date', '>', now()->format('Y-m-d'));
        }
        //sorte
        if ($request->sortOffer == 1) {
            $filter->orderBy( 'expration_date');
        }
        if ($request->sortOffer == 2) {
            $filter->orderByDesc( 'expration_date');
        }
        if ($request->sortCreate == 1) {
            $filter->orderBy('created_at');
        }

        if ($request->sortCreate ==2) {
            $filter->orderByDesc('created_at');

        }
//        if ($request->sortRate === 0) {
//
//            $services = Service::query()->get();
//            foreach ($services as $service) {
//                $service['serv_rate'] = (new RatingController)->operationService($service->id);
//            }
//            $filter->sortByDesc('serv_rate');
        // }
        // $services = Service::query()->get();
//        foreach ($services as $service) {
//            $d = DB::table('services as s')
//                ->join('ratings as r', 'r.service_id', '=', 's.id')
//                ->select('s.id','s.name')
//                ->selectRaw('round(AVG(r.stars)) as compRate')
//                ->where('r.service_id', '=', $service->id)
//                ->groupBy('s.id','s.name')
//                ->get();
//
//        }

        return response()->json($filter->get(), 200);
    }
    public function indexCompany()//user rate
    {
        $companies = Admin::query()->select('name', 'id')->get();


     //   dd("sdkj");
        return response()->json($companies, 200);
    }
//---------------------------------------------------------------------------------

    public function book($date) //admin
    {

        $book = DB::table('orders as o')
            ->join('services as s', 's.id', '=', 'o.service_id')
            ->join('admins as a', 'a.id', '=', 's.admin_id')
            ->select('o.time')
            ->where('date', $date)
            ->where('status', '2')
            ->where('a.user_id', '=', auth()->id())
            ->groupBy('o.time')
            ->get();
        return response()->json($book, 200);
    }

//---------------------------------------------------------------------------------
    public function bookserv($Id, $date) //user
    {

        $book = DB::table('orders as o')
            ->select('o.time', 'o.date')
            ->where('o.service_id', $Id)
            ->where('date', $date)
            ->where('o.status', '2')
            ->groupBy('o.time', 'o.date')
            ->get();
        return response()->json($book, 200);

    }
}
