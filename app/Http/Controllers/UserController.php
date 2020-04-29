<?php

namespace App\Http\Controllers;

use App\User;
use App\Park;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\OrderParam;
use stdClass;
class UserController extends Controller
{
    public function createUser(Request $request){
        $input = $request->all();

        
        $validator = Validator::make($input, [
            'phone' => 'required',
            'password' => 'required'
        ]);

        //Check if this request contains phone and password
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }

        //password encryption
        $pwd = Hash::make($request ->password);
        //Create new token for user
        $token = Str::random(60);
        if (User::where('phone', '=', $request->phone)->exists()) {
            return response()->json([
                'success'=>false, 
                'message'=>'user already exist', 
                'data'=>new stdClass()
            ],200);
        }
        //create user
        $user = User::create([
            'phone' => $request ->phone,
            'password' =>$pwd,
            'token' => $token,
            'carModel'=>'',
            'carNumber'=>'',
            'wallet'=>'1000'
        ]);
        //response data
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
        //Check if this request contains phone and password
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }
        //Get this user
        $user = User::where("phone",$request->phone);
        //Check if this user registrate
        if($user->count()==0){
            return response()->json([
                'success'=>false, 
                'message'=>'No user', 
                'data'=>new stdClass()
            ],200);
        }
        //Check if password is correct
        $user = $user->first();
        if (!Hash::check($request->password, $user->password)) {
            //return response($this::message("Incorect Password",400),400);
            return response()->json([
                'success'=>false, 
                'message'=>'Incorect Password', 
                'data'=>new stdClass()
            ],200);
        }
        //Create new token
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
        //Check if this request contains id ,token, carmodel and carNumber
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }
        //get this user
        $user = User::where("id",$request->id);
        
        if($user->count()==0){
            return response()->json([
                'success'=>false, 
                'message'=>'Id Error', 
                'data'=>new stdClass()
            ],200);
        }
        //check user's token
        $user = $user->first();
        if ($user->token!=$request->token) {
            //return response($this::message("Incorect Password",400),400);
            return response()->json([
                'success'=>false, 
                'message'=>'Token Error', 
                'data'=>new stdClass()
            ],200);
        }
        $carNumbers = User::where('carNumber','=',$request->carNumber)->count();
        if($carNumbers==1){
            $carNumbers = User::where('carNumber','=',$request->carNumber)->first();
            if($carNumbers->id!=$user->id){
                return response()->json([
                    'success'=>false, 
                    'message'=>'Car number exist', 
                    'data'=>new stdClass()
                ],200);
            }
        }
        //save this data to user
        $user ->carModel=$request->carModel;
        $user ->carNumber=$request->carNumber;
        
        $user ->save();
        $user_for_response = [
            'id'=>$user->id,
            'phone'=>$user->phone,
            'token'=>$request->token,
            'carModel'=>$user->carModel,
            'carNumber'=>$user->carNumber,
            'wallet'=>$user->wallet
        ];
        return response()->json([
            'success'=>true, 
            'message'=>'Sucessfully', 
            'data'=>$user_for_response
        ],200);
    }
    public function getUserInfo(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'token' => 'required',
        ]);
        //Check if this request contains id and token
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }
        //get this user
        $user = User::where("id",$request->id);
        
        if($user->count()==0){
            return response()->json([
                'success'=>false, 
                'message'=>'Id Error', 
                'data'=>new stdClass()
            ],200);
        }
        //check user's token
        $user = $user->first();
        if ($user->token!=$request->token) {
            //return response($this::message("Incorect Password",400),400);
            return response()->json([
                'success'=>false, 
                'message'=>'Token Error', 
                'data'=>new stdClass()
            ],200);
        }
        //return user's info
        $user_for_response = [
            'id'=>$user->id,
            'phone'=>$user->phone,
            'token'=>$request->token,
            'carModel'=>$user->carModel,
            'carNumber'=>$user->carNumber,
            'wallet'=>$user->wallet
        ];
        return response()->json([
            'success'=>true, 
            'message'=>'Token Success', 
            'data'=>$user_for_response
        ],200);
    }
    public function updateBalance(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'token' => 'required',
            'wallet'=>'required'
        ]);
        //Check if this request contains id,token and wallet
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }
        //get this user
        $user = User::where("id",$request->id);
        
        if($user->count()==0){
            return response()->json([
                'success'=>false, 
                'message'=>'Id Error', 
                'data'=>new stdClass()
            ],200);
        }
        //check user's token
        $user = $user->first();
        if ($user->token!=$request->token) {
            //return response($this::message("Incorect Password",400),400);
            return response()->json([
                'success'=>false, 
                'message'=>'Token Error', 
                'data'=>new stdClass()
            ],200);
        }
        //update his balance
        $user ->wallet = (int)$user->wallet+(int)$request->wallet;
        $user ->save();
        //return users info
        $user_for_response = [
            'id'=>$user->id,
            'phone'=>$user->phone,
            'token'=>$request->token,
            'carModel'=>$user->carModel,
            'carNumber'=>$user->carNumber,
            'wallet'=>$user->wallet
        ];
        return response()->json([
            'success'=>true, 
            'message'=>'Token Success', 
            'data'=>$user_for_response
        ],200);
    }
    public function getAllOrders(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'token' => 'required',
        ]);
        //Check if this request contains id and token 
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }
        //get this user
        $user = User::where("id",$request->id);
        
        if($user->count()==0){
            return response()->json([
                'success'=>false, 
                'message'=>'Id Error', 
                'data'=>new stdClass()
            ],200);
        }
        //check user's token
        $user = $user->first();
        if ($user->token!=$request->token) {
            //return response($this::message("Incorect Password",400),400);
            return response()->json([
                'success'=>false, 
                'message'=>'Token Error', 
                'data'=>new stdClass()
            ],200);
        }
        //get all his orders
        $Orders = OrderParam::where("userId",$request->id)->get();
        //replace parkId to parkName
        $array = collect();
        foreach($Orders as $ord){
            $name = Park::where('id',$ord->parkId)->first()->name;
            $ord ->parkId = $name;
            $array->push($ord);
        }
        return response()->json([
            'success'=>true, 
            'message'=>'Token Success', 
            'data'=>$array
        ],200);
    }
}
