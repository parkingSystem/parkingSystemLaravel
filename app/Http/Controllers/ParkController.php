<?php

namespace App\Http\Controllers;

use App\User;
use App\Park;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use stdClass;
class ParkController extends Controller
{
    public function getAllParks(Request $request){
        $input = $request->all();


        $validator = Validator::make($input, [
            'id' => 'required',
            'token' => 'required'
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
        $park = Park::all();
        return response()->json([
            'success'=>true, 
            'message'=>'Token Success', 
            'data'=>$park
        ],200);
    } 
    public function getPark(Request $request){
        $input = $request->all();


        $validator = Validator::make($input, [
            'id' => 'required',
            'token' => 'required',
            'parkId'=>'required'
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
        $park = Park::where('id','=',$request->parkId)->first();
        return response()->json([
            'success'=>true, 
            'message'=>'Token Success', 
            'data'=>$park
        ],200);
    }
}
