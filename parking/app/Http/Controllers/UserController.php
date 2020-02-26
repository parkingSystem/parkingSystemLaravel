<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserParam;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function createUser(Request $request){
        $input = $request->all();


        $validator = Validator::make($input, [
            'phone' => 'required',
            'password' => 'required'
        ]);


        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>'Hello'
            ],200);       
        }

        $pwd = Hash::make($request ->password);
        $token = Str::random(60);
        if (User::where('phone', '=', $request->phone)->exists()) {
            return response()->json([
                'success'=>false, 
                'message'=>'user already exist', 
                'data'=>"hello"
            ],200);
        }
        $user = Users::create([
            'phone' => $request ->email,
            'password' =>$pwd,
            'token' => $token,
        ]);
        $user_for_response = [
            'phone'=>$user->phone,
            'token'=>$token,
        ];
        return response()->json([
            'success'=>true, 
            'message'=>'Registrate Sucessfully', 
            'data'=>$user_for_response
        ],200);
    }
}
