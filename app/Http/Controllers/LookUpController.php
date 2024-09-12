<?php

namespace App\Http\Controllers;

use App\Models\Parts;
use Illuminate\Http\Request;
use App\Models\adjustmenRateDetails;
class LookUpController extends Controller
{
    //
    public function getAllProducts(){
      $response =   Parts::where("Status","A")->get();

      if(!$response){
        return response()->json(["StatusCode"=>404,
        "Message"=>"Not Found",
        'Data'=>$response
      ],404);
      }
      return response()->json(["StatusCode"=>200,
        "Message"=>"Success",
        "Data"=>$response->all()],200);
    }
    public function getDiscount(){
      $result = adjustmenRateDetails::where('ACTIVE',1)->get();
      if(!$result){
        return response()->json(['StatusCode'=>404,
                              'Message'=>"Not Found",
                              'Data'=>$result],404);
      }
      return response()->json([ 'StatusCode'=>200,
                                      'Data'=>$result],200);
    }
}
