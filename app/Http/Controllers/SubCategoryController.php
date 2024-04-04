<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Image;
use App\Models\Service;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubCategoryController extends Controller
{
    public function show($IdCategory) //admin +user
    {
        $category = SubCategory::where('category_id', $IdCategory)->get();
        return response()->json($category, Response::HTTP_OK);
    }


    public function showinfo($IdSubCategory) //  admin
    {
        $ad = Admin::where('user_id', auth()->id())->get()->first();
        $service = Service::where([['subCategory_id', $IdSubCategory], ['admin_id', $ad->id]])->get();
        foreach ($service as $services) {
            $adm = Admin::with('services')->where('id', $services->admin_id)->get()->first()->name;
            $services['company_name'] = $adm;


            if (($services['expration_date']) >= (now()->format('Y-m-d'))) {
                $p = $services['discount'];
                $price = $services['price'];
                $p = (($p / 100) * $price);
                $services['sale'] = $price - $p;

            }
            //$service->makehidden('subCategory_id', 'admin_id', 'description','company_name', 'location', 'price', 'discount', 'expration_date')->get();
        $services->makehidden('subCategory_id', 'admin_id','description','company_name','location','discount','sale','expration_date')->get();
            $image = Image::where('service_id', $services->id)
                ->select('id', 'url_image')
                ->get()->first();

            $services['image'] = $image;
        }

        return response()->json($service, Response::HTTP_OK);

    }


    public function index($IdSubCategory)  //user
    {
        $services = Service::where('subCategory_id', $IdSubCategory)->get();

        foreach ($services as $service) {

            $adm = Admin::with('services')->where('id', $service->admin_id)->get()->first()->name;
            $service['company_name'] = $adm;
            $service['serv_rate'] = (new RatingController)->operationService($service->id);

            $service->makehidden('subCategory_id', 'admin_id', 'description','company_name', 'location', 'price', 'discount', 'expration_date')->get();
            $image = Image::where('service_id', $service->id)
                ->select('id', 'url_image')
                ->get()->first();

            $service['image'] = $image;
        }
        return response()->json($services, 200);
    }
}
