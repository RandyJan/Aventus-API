<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderslipHeader extends Model
{
    use HasFactory;
    protected $table = "OrderSlipHeader";

    protected $fillable = [
        "ORDERSLIPNO",
        "BRANCHID",
        "OUTLETID",
        "DEVICENO",
        "ENCODEDBY",
        "PREPAREDBY",
        "CCENAME",
        "CUSTOMERNAME",
        "ACCOUNTTYPE",
        "USER_CURRENT_TRANSACTION",
        "OSTYPE",
        "SC_ID",
        "SC_NAME",
        "SC_ADDRESS",
        "BRANCHID",
        "ENCODEDDATE",
        "OSDATE",
        "TRANSACTTYPEID",
        "TOTALAMOUNT",
        'NETAMOUNT',
        "STATUS",
        "OUTLETID",
        "VATABLE_SALES",
        "VAT_AMAOUNT",
        "SC_DISCOUNT",
        "SC_DISCOUNT_AMOUNT",
        "VAT_EX",
        "OSNUMBER",
        "PAID",
        "BUSDATE",
        "OSTYPE"

    ];
    public static function getNewId($branch_id=null, $outlet_id=null, $device_no=null){
        $result = static::where('BRANCHID', $branch_id)
              ->where('OUTLETID', $outlet_id)
              ->where('DEVICENO', $device_no)
              ->orderBy('ORDERSLIPNO','desc')
              ->first();

          if( is_null($result)){
              return 1;
          }

          return $result->ORDERSLIPNO + 1;
      }
    public  $timestamps = false;
}
