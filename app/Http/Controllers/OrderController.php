<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Park;
use App\Order;
use App\OrderParam;
use stdClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class OrderController extends Controller
{
    public function createOrder(Request $request){
        $input = $request->all();

        //check if request contains userId token and parkId
        $validator = Validator::make($input, [
            'userId' => 'required',
            'token' => 'required',
            'parkId' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }
        $user = User::where("id",$request->userId);
        //check if user is registrate
        if($user->count()==0){
            return response()->json([
                'success'=>false, 
                'message'=>'Id Error', 
                'data'=>new stdClass()
            ],200);
        }
        //check token of user
        $user = $user->first();
        if ($user->token!=$request->token) {
            //return response($this::message("Incorect Password",400),400);
            return response()->json([
                'success'=>false, 
                'message'=>'Token Error', 
                'data'=>new stdClass()
            ],200);
        }
        $parks = Park::where("id",$request->parkId);
        if($parks->count()==0){
            return response()->json([
                'success'=>false, 
                'message'=>'ParkId Error', 
                'data'=>new stdClass()
            ],200);
        }
        if(Order::where("userId",$request->userId)->where("parkId",$request->parkId)->count()>0){
            return response()->json([
                'success'=>false, 
                'message'=>'Order exist', 
                'data'=>new stdClass()
            ],200);
        }
        //get park and get all occupied places.check if park has free places 
        $park = $parks->first();
        $oc_place = (int)($park->occupied_places);
        $all_place = (int)($park->places);
        if($all_place-$oc_place<=0){
            return response()->json([
                'success'=>false, 
                'message'=>'No Places', 
                'data'=>new stdClass()
            ],200);
        }else{
            //if it is has a places reserve for this user 1 place
            $date = date("Y-m-d H:i:s", strtotime(sprintf("+%d hours", 24)));
            $order = Order::create([
                'userId'=>$request->userId,
                'parkId'=>$request->parkId,
                'date'=>$date
            ]);
            $park->occupied_places=$oc_place+1;
            $park->save();
            return response()->json([
                'success'=>true, 
                'message'=>'Token Success', 
                'data'=>$order
            ],200);
        }
    }
    public function validateIn(Request $request){
        $input = $request->all();

        //check if request contains carNumber token and parkId
        $validator = Validator::make($input, [
            'carNumber' => 'required',
            'parkId' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }
        //get user by carNumber
        $user = User::where("carNumber",$request->carNumber);
        if($user->count()<=0){
            return response()->json([
                'success'=>false, 
                'message'=>'Car number error', 
                'data'=>new stdClass()
            ],200);
        }
        $user = $user->first();
        //check if user has a order on this park
        $order = Order::where("userId",$user->id)->where("parkId",$request->parkId);
        if($order->count()>0){
            //if he has delete this order
            Order::where("id",$order->first()->id)->delete();
        }else{
            return response()->json([
                'success'=>false, 
                'message'=>'No Order', 
                'data'=>new stdClass()
            ],200);
        }
        $parks = Park::where("id",$request->parkId);
        if($parks->count()==0){
            return response()->json([
                'success'=>false, 
                'message'=>'ParkId Error', 
                'data'=>new stdClass()
            ],200);
        }
        //create order param for this user and save incoming time
        $park = $parks->first();
        $OrderParam = OrderParam::create([
            'userId'=>$user->id,
            'parkId'=>$request->parkId,
            'incomingTime'=>date("Y-m-d H:i:s"),
            'outgoingTime'=>'0'
        ]);
        FirebaseController::index($user->id,'Вы заехали на парковку:'.$park->name);
        return response()->json([
            'success'=>true, 
            'message'=>'Registrate Sucessfully', 
            'data'=>$OrderParam
        ],200);
    }
    public function validateOut(Request $request){
        $input = $request->all();

        //check if request contains carNumber token and parkId
        $validator = Validator::make($input, [
            'carNumber' => 'required',
            'parkId' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }
        //get user by this carNumber
        $user = User::where("carNumber",$request->carNumber);
        if($user->count()<=0){
            return response()->json([
                'success'=>false, 
                'message'=>'Car number error', 
                'data'=>new stdClass()
            ],200);
        }
        $user = $user->first();
        //find his order
        $order = OrderParam::where("userId",$user->id)->where("parkId",$request->parkId)->where("outgoingTime","0");
        
        if($order->count()!=1){
            return response()->json([
                'success'=>false, 
                'message'=>'no order', 
                'data'=>new stdClass()
            ],200);
        }
        $order = OrderParam::where("userId",$user->id)->where("parkId",$request->parkId)->where("outgoingTime","0")->first();
        $parks = Park::where("id",$request->parkId);
        if($parks->count()==0){
            return response()->json([
                'success'=>false, 
                'message'=>'ParkId Error', 
                'data'=>new stdClass()
            ],200);
        }
        $park = $parks->first();
        //save his outgoing time
        $order->outgoingTime = date("Y-m-d H:i:s");
        $order ->save();
        //give park 1 free place
        $park->occupied_places = $park->occupied_places-1;
        $park ->save();
        //take money from user
        $out = Carbon::parse($order->outgoingTime);
        $in = Carbon::parse($order->incomingTime);

        $diff = $out->diffInMinutes($in);
        (int)$price_per_min = (int)$park->price/60;
        (int)$allTimePrice = $price_per_min*$diff;
        $user ->wallet = (int)$user->wallet - $allTimePrice;
        $user ->save();  
        FirebaseController::index($user->id,'Вы выехали из парковки:'.$park->name);
        return response()->json([
            'success'=>true, 
            'message'=>'Registrate Sucessfully', 
            'data'=>$order
        ],200);
    }
}
