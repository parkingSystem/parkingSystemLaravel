<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use stdClass;
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
                'data'=>new stdClass()
            ],200);       
        }

        $pwd = Hash::make($request ->password);
        $token = Str::random(60);
        if (User::where('phone', '=', $request->phone)->exists()) {
            return response()->json([
                'success'=>false, 
                'message'=>'user already exist', 
                'data'=>new stdClass()
            ],200);
        }
        $user = User::create([
            'phone' => $request ->phone,
            'password' =>$pwd,
            'token' => $token,
            'carModel'=>'',
            'carNumber'=>'',
            'wallet'=>''
        ]);
        $user_for_response = [
            'id'=>$user->id,
            'phone'=>$user->phone,
            'token'=>$token,
        ];
        return response()->json([
            'success'=>true, 
            'message'=>'Registrate Sucessfully', 
            'data'=>$user_for_response
        ],200);
    }
    public function login(Request $request){
        $input = $request->all();


        $validator = Validator::make($input, [
            'phone' => 'required',
            'password' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }
        $user = User::where("phone",$request->phone);
        
        if($user->count()==0){
            return response()->json([
                'success'=>false, 
                'message'=>'No user', 
                'data'=>new stdClass()
            ],200);
        }
        $user = $user->first();
        if (!Hash::check($request->password, $user->password)) {
            //return response($this::message("Incorect Password",400),400);
            return response()->json([
                'success'=>false, 
                'message'=>'Incorect Password', 
                'data'=>new stdClass()
            ],200);
        }
        $token = Str::random(60);
        $user ->token = $token;
        
        $user ->save();
        $user_for_response = [
            'id'=>$user->id,
            'phone'=>$user->phone,
            'token'=>$token,
            'carModel'=>$user->carModel,
            'carNumber'=>$user->carNumber,
            'wallet'=>'1000'
        ];
        return response()->json([
            'success'=>true, 
            'message'=>'Login Sucessfully', 
            'data'=>$user_for_response
        ],200);
        
    } 
    public function updateUser(Request $request){
        $input = $request->all();


        $validator = Validator::make($input, [
            'id' => 'required',
            'token' => 'required',
            'carModel' => 'required',
            'carNumber' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }
        $user = User::where("id",$request->id);
        
        if($user->count()==0){
            return response()->json([
                'success'=>false, 
                'message'=>'Id Error', 
                'data'=>new stdClass()
            ],200);
        }
        $user = $user->first();
        if ($user->token!=$request->token) {
            //return response($this::message("Incorect Password",400),400);
            return response()->json([
                'success'=>false, 
                'message'=>'Token Error', 
                'data'=>new stdClass()
            ],200);
        }
        if(User::where('carNumber','=',$request->carNumber)->exist()){
            return response()->json([
                'success'=>false, 
                'message'=>'Car number exist', 
                'data'=>new stdClass()
            ],200);
        }
        $token = Str::random(60);
        $user ->carModel=$request->carModel;
        $user ->carNumber=$request->carNumber;
        
        $user ->save();
        $user_for_response = [
            'id'=>$user->id,
            'phone'=>$user->phone,
            'token'=>$token,
            'carModel'=>$user->carModel,
            'carNumber'=>$user->carNumber,
            'wallet'=>'1000'
        ];
        return response()->json([
            'success'=>true, 
            'message'=>'Login Sucessfully', 
            'data'=>$user_for_response
        ],200);
    }
}
