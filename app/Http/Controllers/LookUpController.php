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
        return response()->json(["Status"=>404],404);
      }
      return response()->json($response->all(),200);
    }
    public function getDiscount(){
      $result = adjustmenRateDetails::where('ACTIVE',1)->get();
      return response()->json($result,200);
    }
}
