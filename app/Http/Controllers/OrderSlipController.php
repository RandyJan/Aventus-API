<?php

namespace App\Http\Controllers;

use App\Models\adjustmenRateDetails;
use App\Models\OrderslipDetail;
use App\Models\OrderslipHeader;
use App\Models\SiteParts;
use App\Models\transactionDetails;
use App\Models\TransactionHeader;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\traits\auth;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

use function PHPUnit\Framework\isEmpty;

class OrderSlipController extends Controller
{
    //
    use auth;
    public function addNewJobOrder(Request $request)
    {
        DB::beginTransaction();
        $orderslipNumber = OrderslipHeader::getNewId($request['BRANCHID'], $request['OUTLETID'], $request['DEVICENO']);
        if ($this->basicauth($request->header('Authorization', 'default'))) {
            try {
                $request->validate([
                    'OSNUMBER'=>'required|unique:OrderslipHeader',
                    'ACCOUNTTYPE'=>'required|integer',
                    'CUSTOMERNAME'=>'required|string',
                    'items'=>'required',
                ],[
                    'OSNUMBER.unique'=>'JONUMBER is already taken',
                    'OSNUMBER.required'=>'JONUMBER field is required',
                    'CUSTOMERNAME.required'=>'CUSTOMER_NAME field is required',
                    'ACCOUNTTYPE.required'=>'ACCOUNTTYPE field is required'
                ]);
                    if($request['IS_SC']){
                        $request->validate(['SC_DISCOUNT_PERCENTAGE'=>'required',
                        'SC_DISCOUNT_AMOUNT'=>'required'], [
                            'SC_DISCOUNT_AMOUNT.required' => 'The SC_DISCOUNT_AMOUNT field is required.',
                            'SC_DISCOUNT_PERCENTAGE.required' => 'The SC_DISCOUNT_PERCENTAGE field is required.',
                        ]);
                    }
                    Log::info($request->all());
                $date = Carbon::now();
                $headerData =   OrderslipHeader::create([
                    "CUSTOMERNAME" => $request['CUSTOMERNAME'],
                    "OSNUMBER" => $request["OSNUMBER"],
                    "DEVICENO" => $request['DEVICENO'],
                    "ACCOUNTTYPE" => $request["ACCOUNTTYPE"],//for review
                    "CCENAME" => $request["ENCODER"],
                    "ENCODEDBY" => $request["ENCODER"],//Encoder
                    "PREPAREDBY" => $request['ENCODER'],
                    // "USER_CURRENT_TRANSACTION" => $request['USER_CURRENT_TRANSACTION'],
                    "OSTYPE" => 1,//identifier of the request's origin 1 api 0 ambulant
                    "BRANCHID" => $request['BRANCHID'],
                    "ENCODEDDATE" => $date,
                    "OSDATE" => $date,
                    "TRANSACTTYPEID" => $request['TRANSACTTYPEID'],
                    "TOTALAMOUNT" => $request['TOTALAMOUNT'],
                    'NETAMOUNT' => $request['NETAMOUNT'],
                    "STATUS" => $request['STATUS'],
                    "OUTLETID" => $request['OUTLETID'],
                    "VATABLE_SALES" => $request['VATABLE_SALES'],
                    "VAT_AMOUNT" => $request['VAT_AMOUNT'],
                    "SC_DISCOUNT_PERCENTAGE" => $request['SC_DISCOUNT_PERCENTAGE'],
                    "SC_DISCOUNT_AMOUNT" => $request['SC_DISCOUNT_AMOUNT'],
                    "VAT_EX" => $request['VAT_EX'],
                    "ORDERSLIPNO" => $orderslipNumber,
                    "PAID" => 0,
                    "DATE" => OrderslipDetail::getClarionDate($date),
                    "BUSDATE" => $date,
                    "CUSTTIN"=>$request['CUSTOMERNO'],
                    "CUSTADDRESS"=>$request["CUSTOMERADDRESS"],
                    "IS_SC"=>$request["SCPWD"]
                    
                ]);

                $line_number = OrderSlipDetail::getNewLineNumber($request['OSNUMBER']);
                foreach ($request['items'] as $items) {
                    $isActive = SiteParts::where('PRODUCT_ID', $items['PRODUCT_ID'])->where('STATUS', 'A ')->first();
                    $isDiscount = adjustmenRateDetails::where('ID',$items['DISCID'])->where('ACTIVE','1')->first();
                    // return $isDiscount;
                    if ($isActive) {
                        if (!$items['DISCID'] == ""||!$items['DISCID'] ==null) {
                            if (!$isDiscount) {
                                DB::rollBack();
                                return response()->json([
                                    'StatusCode' => 404,
                                    'Message' => 'Discount is not active or does not exists',
                                    "DISCID" => $items['DISCID']
                                ], 404);
                            }
                        }
                        if(env('BRANCH')!= $request['BRANCHID']){
                            DB::rollBack();
                            return response()->json([
                                'StatusCode' => 500,
                                'Message' => 'BRANCHID dont match on the current branch',
                                "DISCID" => $request['BRANCHID']
                            ], 500);

                        }
                        $retval = OrderslipDetail::where('OSNUMBER',$request['OSNUMBER'])->orderBy('POSLINENO','desc')->first();
                        $ret = 1;
                        if($retval){
                            $ret = $retval->ORDERSLIPDETAILID+1;
                        }

                        $itemData =   OrderslipDetail::insert([
                            'ORDERSLIPDETAILID' => $ret,
                            'ORDERSLIPNO' => $orderslipNumber,
                            'BRANCHID' => $request['BRANCHID'],
                            'OUTLETID' => $request['OUTLETID'],
                            'DEVICENO' => $request['DEVICENO'],
                            'OSNUMBER' => $request["OSNUMBER"],
                            // 'MAIN_OSNUMBER' => $items['OSNUMBER'],
                            'PRODUCT_ID' => $items['PRODUCT_ID'],
                            // 'PARTNO' => $items['PARTNO'],
                            'RETAILPRICE' => $items['RETAILPRICE'],
                            'QUANTITY' => $items['QUANTITY'],
                            'REQUESTEDQTY' => $items['QUANTITY'],
                            'AMOUNT' => $items['AMOUNT'],
                            'NETAMOUNT' => $items['NETAMOUNT'],
                            // 'REMARKS' => $request->notes,
                            'LINE_NO' => $ret,
                            'ORNO' => $ret,
                            // 'OSTYPE' => $$items['OSTYPE'],
                            'STATUS' => 'X',
                            // 'SEQUENCE' => $sequence,
                            'OSDATE' => OrderslipDetail::getClarionDate($date),
                            // 'LOCATIONID' => $items['GROUP_CODE'],
                            'ENCODEDDATE' => $date,
                            'DISPLAYMONITOR' => 1,
                            'POSLINENO' => $ret,
                            // 'OS_SC_ID' => $items['OS_SC_ID'],
                            'DISCID' => $items['DISCID'],
                            // 'PRODUCTGROUP' => $items['LOCATION'],
                            'VATABLE_SALES' => $items['VATABLE_SALES'],
                            'VAT_AMOUNT' => $items['VAT_AMOUNT'],
                              "VAT_EX" => $items['VAT_EX'],
                            'SC_DISCOUNT_PERCENTAGE' => $items['SC_DISCOUNT_PERCENTAGE'],
                            'SC_DISCOUNT_AMOUNT' => $items['SC_DISCOUNT_AMOUNT'],
                            'PSTATUS' => 0,
                            'DISCOUNT'=>$items['DISCOUNT'],
                            'SC_COUNT'=>$request['SCPWD']>0?1:null
                        ]);
                        Log::info($request->all());
                        DB::commit();
                    } else {
                        // Log::info("Failed product does not exists"+$items['PRODUCT_ID']);
                        DB::rollBack();
                        return response()->json([
                            'StatusCode' => 404,
                            "Message" => "Product is not active or does not exists",
                            "Product_ID" => $items['PRODUCT_ID']
                        ], 404);
                    }
                }
                return response()->json([
                    'StatusCode' => 200,
                    'Message' => 'Success',
                    'JONUMBER' => $request['OSNUMBER']

                ], 200);
            } catch (ValidationException $e) {
                DB::rollBack();

                return response()->json([
                    'StatusCode' => 500,
                    'Message' => $e->getMessage(),
                    'Details' =>$e->errors(),

                ], 500);
            }
        } else {
            return response()->json([
                'StatusCode' => 401,
                'Message' => "Unauthorized Access"
            ], 401);
        }
    }

    public function sumrpt(Request $request,$branch,$date){
         if ($this->basicauth($request->header('Authorization', 'default'))) {
            $res = TransactionHeader::leftJoin('TransactionDetails', 'TransactionDetails.TRHID', '=', 'TransactionHeader.TRHID')
            ->leftJoin('OrderSlipDetails', 'TransactionHeader.ORDERSLIPNO', '=', 'OrderSlipDetails.OSNUMBER')
            ->leftJoin('OrderSlipHeader', 'OrderSlipHeader.OSNUMBER', '=', 'OrderSlipDetails.OSNUMBER')
            ->leftJoin('SiteParts', 'SiteParts.PRODUCT_ID', '=', 'OrderSlipDetails.PRODUCT_ID')
            ->select(
                'TransactionDetails.SEQ',
                'TransactionDetails.TRHID',
                'OrderSlipDetails.ORDERSLIPNO as ORNUMBER',
                DB::raw("FORMAT(DATEADD(DAY, CAST(OrderSlipDetails.ENCODEDDATE AS INT), '1900-01-01'), 'yyyy-MM-dd') as DATE"), // Convert ENCODEDDATE
                'OrderSlipDetails.OSNUMBER as JONUMBER',
                'OrderSlipHeader.ENCODEDBY as CASHIERNAME',
                'OrderSlipHeader.CUSTOMERNAME as PATIENTNAME',
                'OrderSlipHeader.ACCOUNTTYPE as COMPANY',
                'OrderSlipDetails.AMOUNT',
                DB::raw("COALESCE(CAST(OrderSlipDetails.DISCOUNT AS FLOAT), 0) as DISCOUNT"),
                DB::raw('CAST(OrderSlipDetails.SC_DISCOUNT_AMOUNT AS FLOAT)  as SCPWDDISCOUNT'),
                'OrderSlipDetails.NETAMOUNT',
                DB::raw('CAST(OrderSlipDetails.VATABLE_SALES AS FLOAT) as VATABLE'),
                DB::raw('CAST(OrderSlipDetails.VAT_EX AS FLOAT) as VATEXEMPT'),
                DB::raw('CAST(VATAMOUNT AS FLOAT)as VAT '),
                'SiteParts.SHORTCODE as PARTICULARS'
            )
            ->where('OrderSlipDetails.BRANCHID', $branch)
            ->where('OrderSlipDetails.STATUS', 'X')
            ->where(DB::raw("FORMAT(DATEADD(DAY, CAST(OrderSlipDetails.ENCODEDDATE AS INT), '1900-01-01'), 'yyyy-MM-dd')"), '>=', $date)
            ->where('TransactionDetails.INFO', 'I [ ')
            ->get();
        $mopRes = [];
        $trhid = 0;
        $seq= 0;
        
        if(count($res)<1 || !$res){
            return response()->json(['StatusCode'=>404,'Message'=>'Not found!','BRANCHID'=>$branch,'DATE'=>$date],404);
        }
        foreach($res as $itemmop){
            if($trhid != $itemmop['TRHID'] && $seq != $itemmop['SEQ'] ){

                $mop = transactionDetails::where('TRHID',$itemmop['TRHID'])->where('INFO','P')
                ->orWhere('INFO','P L')
                // ->orWhere('INFO','C')
                ->get();
                foreach($mop as $mopitem){
                    $change = transactionDetails::where('TRHID',$mopitem['TRHID'])->where('INFO','C')->get();
                    $value = $mopitem['TOTAL'];
                    if(count($change) > 0 && trim($mopitem['DESC']) == 'CASH'){
                        $value = $mopitem['TOTAL']-$change[0]['TOTAL'];
                    }
                    $mopRes[]=['TRHID'=>$mopitem['TRHID'],'DESCRIPTION'=>$mopitem['DESC'],'TOTAL'=>$value];
                }
            }
            $trhid = $itemmop['TRHID'];
            $seq = $itemmop['SEQ'];
        }
        return response()->json(['StatusCode'=>200,
                                        'Message'=>'Success',
                                        'BRANCHID'=>$branch,
                                        'DATE'=>$res[0]['DATE'],
                                        'TRANSACTIONS'=>$res,
                                        'MOP'=>$mopRes]
            );
    }else {
        return response()->json([
            'StatusCode' => 401,
            'Message' => "Unauthorized Access"
        ], 401);
    }


}
    
}
