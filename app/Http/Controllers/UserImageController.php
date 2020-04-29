<?php

namespace App\Http\Controllers;

use App\User;
use App\UserImages;
use App\Park;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use stdClass;

class UserImageController extends Controller
{
    public function saveIncomingImage(Request $request){
        $input = $request->all();


        $validator = Validator::make($input, [
            'id' => 'required',
            'parkId' => 'required',
            'comingImage' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }
        if($request->hasfile('comingImage')) 
        { 
            $file = $request->file('comingImage');
            $extension = $file->getClientOriginalExtension(); 
            $filename =time().'.'.$extension;
            $file->move('uploads/logos/', $filename);
        }else{
            return response()->json([
                'success'=>false, 
                'message'=>'No image', 
                'data'=>new stdClass()
            ],200); 
        }
        $userImage = UserImages::create([
            'userId' => $request->id,
            'parkId' => $request->parkId,
            'comingImage' => 'uploads/logos/'. $filename,
            'outgoingImage' => '',
            'comingTime' => date("Y-m-d H:i:s"),
            'outgoingTime' => '',
        ]);

        return response()->json([
            'success'=>true, 
            'message'=>'Image stored', 
            'data'=>$userImage
        ],200);

    }public function saveOutgoingImage(Request $request){
        $input = $request->all();


        $validator = Validator::make($input, [
            'imageId' => 'required',
            'outgoingImage' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false, 
                'message'=>'Bad Request', 
                'data'=>new stdClass()
            ],200);       
        }
        if($request->hasfile('outgoingImage')) 
        { 
            $file = $request->file('outgoingImage');
            $extension = $file->getClientOriginalExtension(); 
            $filename =time().'.'.$extension;
            $file->move('uploads/logos/', $filename);
        }else{
            return response()->json([
                'success'=>false, 
                'message'=>'No image', 
                'data'=>new stdClass()
            ],200); 
        }
        $userImage = UserImages::where('id','=',$request->imageId);
        if($userImage->count()>0){
            $userImage = $userImage->first();
            $userImage ->outgoingImage = 'uploads/logos/'. $filename;
            $userImage ->date("Y-m-d H:i:s");
            $userImage ->save();
        }else{
            return response()->json([
                'success'=>false, 
                'message'=>'No id', 
                'data'=>new stdClass()
            ],200);
        }

        return response()->json([
            'success'=>true, 
            'message'=>'Image stored', 
            'data'=>$userImage
        ],200);

    }
}
