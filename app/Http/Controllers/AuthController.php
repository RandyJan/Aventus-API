<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\userDevices;
class AuthController extends Controller
{
    //
    public function login(Request $req){

        $res =userDevices::select()->where("ID",$req["id"])->where("PW",$req["pw"])->first();

        if($res){
            return response()->json(["Status"=>"1","data"],200);
        }
        // $responseJson = json_encode(array(
        //     "status" => "Error"
        // ));
        return response()->json(["Status"=>"Error"],401);

    }
}
