<?php

namespace App\Http\Controllers;

use App\User;
use App\Park;
use App\Order;
use App\Http\Controllers\FirebaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use stdClass;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\ServiceAccount;
class ParkController extends Controller
{
    public function getAllParks(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'token' => 'required'
        ]);
        //check if request contains id and token
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }
        $user = User::where("id",$request->id);
        //check if this user registrate
        if($user->count()==0){
            return response()->json([
                'success'=>false, 
                'message'=>'Id Error', 
                'data'=>new stdClass()
            ],200);
        }
        //check if token is correct
        $user = $user->first();
        if ($user->token!=$request->token) {
            return response()->json([
                'success'=>false, 
                'message'=>'Token Error', 
                'data'=>new stdClass()
            ],200);
        }
        //take all parks and return it
        $park = Park::all();
        return response()->json([
            'success'=>true, 
            'message'=>'Token Success', 
            'data'=>$park
        ],200);   
    } 
    
    public function getPark(Request $request){
        $input = $request->all();
        //check if request contains id and token
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
        //check if this user registrate
        if($user->count()==0){
            return response()->json([
                'success'=>false, 
                'message'=>'Id Error', 
                'data'=>new stdClass()
            ],200);
        }
        //check if token is correct
        $user = $user->first();
        if ($user->token!=$request->token) {
            
            return response()->json([
                'success'=>false, 
                'message'=>'Token Error', 
                'data'=>new stdClass()
            ],200);
        }
        //find the park bt id
        $park = Park::where('id','=',$request->parkId)->first();
        //check if orders is not expired
        $orders = Order::where('parkId','=',$request->parkId)->get();
        $count = 0;
        foreach($orders as $order){
            if($order->date<=date("Y-m-d H:i:s")){
                Order::where('id',$order->id)->delete();
                $count = $count+1;
            }
        }
        $park->occupied_places = $park->occupied_places-$count;
        $park ->save();
        return response()->json([
            'success'=>true, 
            'message'=>'Token Success', 
            'data'=>$park
        ],200);
    }
}
