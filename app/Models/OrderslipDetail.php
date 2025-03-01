<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class OrderslipDetail extends Model
{
    use HasFactory;
    protected $table = "OrderSlipDetails";

    /**
     * Convert Clarion date to YYYY-MM-DD format.
     *
     * @param int $clarionDate
     * @return string|null
     */
    protected $fillable =
    [
        "PRODUCT_ID",
        "RETAILPRICE",
        "QUANTITY",
        "REQUESTEDQTY",
        "AMOUNT",
        "NETAMOUNT",
        "REMARKS",
        "PARTNO",
        "LOCATIONID",
        "MAIN_PRODUCT_ID",
        "OS_SC_ID",
        "VATABLE_SALES",
        "VAT_AMOUNT",
        "VAT_EX",
        "SC_DISCOUNT_PERCENTAGE",
        "SC_DISCOUNT_AMOUNT",
        "SC_COUNT",
        "GROUP_SERVING",
        "PRODUCTGROUP",
        "OSNUMBER",
        'DISCID',
        'DISCOUNT',
        'SC_COUNT'
    ];
    public static function getNewDetailId($orderslip_number)
    {

        $result = static::where('OSNUMBER', $orderslip_number)
            ->orderBy('LINE_NO', 'desc')
            ->first();
        Log::info($result);
        if (!$result) {
            Log::info("true");
            return 1;
        }
        $test = $result->ORDERSLIPDETAILID;
        return $test+1 ;
    }
    public static function getNewLineNumber($orderslip_number)
    {
        $result =  static::where('OSNUMBER', $orderslip_number)
            ->orderby('LINE_NO', 'desc')
            ->first();

        if ($result == null) {
            return 1;
        }

        return $result->LINE_NO + 1;
    }
    public static function getClarionDate($date){
        $start_date = '1801-01-01';
        $start_from = \Carbon\Carbon::parse($start_date);
        $diff = $date->diffInDays($start_from) + 4;
        return $diff;
    }
    public function convertClarionDateToDate($clarionDate)
    {
        $baseDate = Carbon::createFromDate(1900, 1, 1);
        
        $actualDate = $baseDate->addDays($clarionDate - 1);

        return $actualDate->format('Y-m-d');
    }
    public  $timestamp = false;
}
