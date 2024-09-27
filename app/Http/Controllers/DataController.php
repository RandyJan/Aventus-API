<?php

namespace App\Http\Controllers;

use App\Models\adjustmenRateDetails;
use App\Models\SiteParts;
use Illuminate\Http\Request;
use App\traits\auth;
use function PHPUnit\Framework\isEmpty;
use App\Models\Parts;
use PHPUnit\Framework\Constraint\Count;

class DataController extends Controller
{
    use auth;
    //
    public function dispatchData(Request $request){
        if($this->basicauth($request->header('Authorization','default'))){

            if($request['Products']!= 0){
                $result = SiteParts::updateOrInsert($request['Products']);
                if($result){
                    return 'success';
                }
                return 'failed';
            }

        }
        else{
            return response()->json([
                'StatusCode'=>401,
                'Message'=>'Unauthorized access'
            ],401);
        }
    }
}
