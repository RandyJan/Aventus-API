<?php

namespace App\Http\Controllers;

use App\Models\Parts;
use Illuminate\Http\Request;
use App\Models\adjustmenRateDetails;
use App\Models\SiteParts;
use App\Traits\auth;
class LookUpController extends Controller
{
  use auth;
    //
    public function getAllProducts(Request $request){
      if($this->basicauth($request->header('Authorization', 'default'))){
      $response =   SiteParts::where("Status","A")->limit(100)->get();

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
    return response()->json([
      'StatusCode' => 401,
      'Message' => "Unauthorized Access"
  ], 401);
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
