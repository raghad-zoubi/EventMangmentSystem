<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{

    public function index($id)
    {

        $comment = DB::table('comments as co')
            ->join('customers as cu', 'cu.id', '=', 'co.customer_id')
            ->join('users as u', 'u.id', '=', 'cu.user_id')
            ->select('co.id','co.comment','u.name')
            ->where('co.service_id', '=', $id)
            ->groupBy('co.id','co.comment','u.name')
            ->get();

        return response()->json($comment, 200);
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'comment' => ['required', 'string', 'min:1', 'max:500'],
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $customer = Customer::where('user_id', auth()->id())->get('id')->first();

        $comment = Comment::query()->create([
            'comment' => $request->comment,
            'customer_id' => $customer->id,
            'service_id' => $id
        ]);
        $comment->save();
        return response()->json($comment, 201);
    }


    public function update(Request $request, $IdComment)
    {
        $validator = Validator::make($request->all(), [
            'value' => ['required', 'string', 'max:225'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }

        $customer = Customer::where('user_id', auth()->id())->get()->first();
        $comment = Comment::find($IdComment);

        if ($customer->id == $comment->customer_id) {

            $comment = new Comment();
            $comment->value = $request->value;
            $comment->customer_id = $customer->id;

            $comment->save();
            return response()->json($comment, 200);
        }
        return response()->json("error", 404);
    }


    public function destroy($IdComment)
    {
        $customer = Customer::where('user_id', auth()->id())->get()->first();

        $comment = Comment::find($IdComment);

        if ($customer->id == $comment->customer_id) {
            $comment->delete();
            return response()->json("success", 200);
        }
        return response()->json("error", 404);
    }
}
