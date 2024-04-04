<?php

namespace App\Http\Controllers;

use App\Models\{Admin, Customer, SuperAdmin, User};
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
            'c_password' => ['required', 'same:password'],
            'role' => ['required'],
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $request['password'] = Hash::make($request['password']);

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,

        ]);

        if ($request->role == 1) {
            $super = SuperAdmin::query()->create([
                'role' => $request->role,
                'user_id' => $user->id
            ]);
        }
        if ($request->role == 2 || $request->role == 0) {
            $admin = Admin::query()->create([
                'role' => $request->role,
                'user_id' => $user->id
            ]);
        }

        if ($request->role == 3) {
            $customer = Customer::query()->create([
                'role' => $request->role,
                'user_id' => $user->id
            ]);
        }

        $tokenResult = $user->createToken('PersonalAccessToken');

        $data["user"] = $user;
        $data["token_type"] = 'Bearer';
        $data["access_token"] = $tokenResult->accessToken;

        return response()->json($data, 200);
    }

//________________________________________________________________________

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);

        }

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {

            throw new AuthenticationException();
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $data["user"] = $user;
        $data["token_type"] = 'Bearer';
        $data["access_token"] = $tokenResult->accessToken;
        return response()->json($data, 200);

    }

    //______________________________________________________________________________

    public function logout(Request $request)
    {
        $result = $request->user()->token()->revoke();
        if ($result) {
            $response = response()->json(['message' => 'User logout successfully.'], 200);
        } else {
            $response = response()->json(['message' => 'Something is wrong.'], 400);
        }
        return $response;
    }

    //_______________________________________________________________________________________________

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all());
        }

        $current_user = User::where('id', auth()->id())->get()->first();
        if (Hash::check($request->password, $current_user->password)) {

            $re = $request->user()->delete();
            if ($re) {
                $response = response()->json(['message' => 'User delete successfully.'], 200);
            }
        } else {

            $response = response()->json(['message' => 'Something is wrong.'], 400);
        }
        return $response;
    }

    //___________________________________________________________________________

    public function change(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => ['required', 'min:8'],
            'new_password' => ['required', 'min:8'],
            'c_password' => ['required', 'same:new_password'],
        ]);
        if ($validator->fails()) {

            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $current_user = User::where('id', auth()->id())->get()->first();
        if (Hash::check($request->old_password, $current_user->password)) {

            $request['new_password'] = Hash::make($request['new_password']);
            $current_user->update([
                'password' => $request->new_password,
            ]);
            $response = response()->json(['message' => 'User change successfully.'], 200);
        } else {
            $response = response()->json(['message' => 'Something is wrong.'], 400);
        }
        return $response;
    }

    //_____________________________________________________________________________________________

    public function check(Request $request)
    {
        $current = Admin::where('user_id', auth()->id())->get()->first();
     $current->makehidden('replay_speed', 'delivery_speed', 'description', 'user_id', 'name', 'url_img')->get();

        return response()->json($current, 200);
    }
//------------------------------------------------------------------------------------
    public function fcmToken(Request $request)
    {
        $user = User::find(auth()->id());
        $user->update(['fcm_token' => $request->fcm_token]);
        return response()->json('fcm updated successfully', 200);
    }

}
