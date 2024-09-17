<?php

namespace App\Http\Controllers;

use App\Models\OrderslipDetail;
use App\Models\OrderslipHeader;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderSlipController extends Controller
{
    //
    public function addNewJobOrder(Request $request)
    {
        $username = env('USER_NAME');
        $password = env('PASSWORD');
        $authHeader = 'Basic ' . base64_encode($username . ':' . $password);
        Log::info($request->all());
        $headerInfo = $request->all();
        //  return $request->all();
        // Log::info($request->header('Authorization', 'default'));
        $orderslipNumber = OrderslipHeader::getNewId($request['BRANCHID'],$request['OUTLETID'],$request['DEVICENO']);
        if ($request->header('Authorization', 'default') == $authHeader) {
            // try {
                $date = Carbon::now();
                    $headerData =   OrderslipHeader::create([
                        "CUSTOMERNAME" => $request['CUSTOMERNAME'],
                        "OSNUMBER" => $request["OSNUMBER"],
                        "DEVICENO" => $request['DEVICENO'],
                        "ACCOUNTTYPE" => $request["ACCOUNTTYPE"],
                        "CCENAME" => $request["CCENAME"],
                        "ENCODEDBY" => $request["ENCODEDBY"],
                        "PREPAREDBY" => $request['PREPAREDBY'],
                        "USER_CURRENT_TRANSACTION" => $request['USER_CURRENT_TRANSACTION'],
                        "OSTYPE" => $request['OSTYPE'],
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
                        "ORDERSLIPNO"=>$orderslipNumber,
                        "PAID"=>0,
                        "BUSDATE"=>$date
                    ]);

                    $line_number = OrderSlipDetail::getNewLineNumber($request['OSNUMBER']);
                    foreach ($request['items'] as $items) {

                        $itemData =   OrderslipDetail::insert([
                            'ORDERSLIPDETAILID' => OrderSlipDetail::getNewDetailId($request['OSNUMBER']),
                            'ORDERSLIPNO' => $orderslipNumber,
                            'BRANCHID' => $items['BRANCHID'],
                            'OUTLETID' => $items['OUTLETID'],
                            'DEVICENO' => $items['DEVICENO'],
                            'OSNUMBER' => $items['OSNUMBER'],
                            // 'MAIN_OSNUMBER' => $items['OSNUMBER'],
                            'PRODUCT_ID' => $items['PRODUCT_ID'],
                            'PARTNO' => $items['PARTNO'],
                            'RETAILPRICE' => $items['RETAILPRICE'],
                            'QUANTITY' => $items['QUANTITY'],
                            'REQUESTEDQTY' => $items['REQUESTEDQTY'],
                            'AMOUNT' => $items['AMOUNT'],
                            'NETAMOUNT' => $items['NETAMOUNT'],
                            // 'REMARKS' => $request->notes,
                            'LINE_NO' => $line_number,
                            'ORNO' => $line_number,
                            // 'OSTYPE' => $$items['OSTYPE'],
                            'STATUS' => 'X',
                            // 'SEQUENCE' => $sequence,
                            'OSDATE' => OrderslipDetail::getClarionDate($date),
                            'LOCATIONID' => $items['GROUP_CODE'],
                            'ENCODEDDATE' => $date,
                            'DISPLAYMONITOR' => 1,
                            'POSLINENO' => $line_number,
                            'OS_SC_ID' => $items['OS_SC_ID'],
                            'DISCID' => $items['DISCID'],
                            'PRODUCTGROUP' => $items['LOCATION'],


                            'VATABLE_SALES' => 0,
                            'VAT_AMOUNT' => 0,
                            'SC_DISCOUNT_PERCENTAGE' => 0,
                            'SC_DISCOUNT_AMOUNT' => 0,
                            'PSTATUS' => 0
                        ]);
                    
                }
                return response()->json([
                    'StatusCode' => 200,
                    'Message' => 'Success',
                    'OSNUMBER'=>$request['OSNUMBER']

                ], 200);
            // } catch (Exception $e) {

            //     return response()->json([
            //         'StatusCode' => 500,
            //         'Message' => 'Error please try again',
            //         'Details' => $e
            //     ], 500);
            // }
        } else {
            return response()->json([
                'StatusCode' => 401,
                'Message' => "Unauthorized Access"
            ], 401);
        }
    }
}
