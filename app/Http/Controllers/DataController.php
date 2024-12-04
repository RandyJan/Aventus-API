<?php

namespace App\Http\Controllers;

use App\Models\adjustmenRateDetails;
use App\Models\SiteParts;
use Illuminate\Http\Request;
use App\traits\auth;
use function PHPUnit\Framework\isEmpty;
use App\Models\Parts;
use Exception;
use Illuminate\Database\QueryException;
use PHPUnit\Framework\Constraint\Count;

class DataController extends Controller
{
    use auth;
    //
    public function dispatchData(Request $request)
    {
        if ($this->basicauth($request->header('Authorization', 'default'))) {

            $resProducts = [];
            if (count($request['Products']) != 0) {
                foreach ($request['Products'] as $items) {
                    try {


                        $result = SiteParts::updateOrInsert(['ARNOC' => $items['BRANCHID'], 'PRODUCT_ID' => $items['PRODUCTID']], [
                            'ARNOC' => $items['BRANCHID'],
                            'PRODUCT_ID' => $items['PRODUCTID'],
                            'MASTERCODE' => $items['LEVEL1'],
                            'BSUNITCODE' => $items['LEVEL2'],
                            'GROUP' => $items['LEVEL3'],
                            'CATEGORY' => $items['LEVEL4'],
                            'PRODUCTNO' => $items['LEVEL5'],
                            // 'PRODUCTCODE' => $items['PRODUCTCODE'],
                            'PARTNO' => $items['PARTNO'],
                            'DESCRIPTION' => $items['DESCRIPTION'],
                            // 'EDC_CODES' => $items['EDC_CODES'],
                            // 'STANDARD' => $items['STANDARD'],
                            'SHORTCODE' => $items['SHORTCODE'],
                            // 'QUANTITY' => $items['QUANTITY'],
                            // 'COST' => $items['COST'],
                            // 'NETCOST' => $items['NETCOST'],
                            'RETAIL' => $items['RETAIL'],
                            'NETRETAIL' => $items['NETRETAIL'],
                            // 'CREDIT' => $items['CREDIT'],
                            // 'PROVINCE' => $items['PROVINCE'],
                            'STATUS' => "A",
                            'VAT'=>$items['VATABLE'],
                            'STDCARCASSWEIGHT'=>$items['SCDISCRATE'],
                            'TIMPLADOS'=>$items['SCVATEX']
                        ]);

                        $resProducts[] = [
                            'StatusCode' => 200,
                            'Message' => 'Success',
                            'ProductID' => $items['PRODUCTID']
                        ];
                    } catch (QueryException $e) {
                        $resProducts[] = [
                            'StatusCode' => 500,
                            'Message' => 'Failed',
                            'ProductID' => $items['PRODUCTID'],
                            'Details' => $e
                        ];
                    }
                }
                if (($request['Discounts']) != 0) {
                    try {


                        foreach ($request['Discounts'] as $items) {
                            $maxId = adjustmenRateDetails::max('ID');
                            $maxId++;
                            $result = adjustmenRateDetails::updateOrInsert(['ID' => $items['ID']], [
                                'ISGROSS' => $items['ISGROSS'],
                                'ID' => $items['ID'] ?? $maxId,
                                'DESCRIPTION' => $items['DESCRIPTION'],
                                'SHORTDESCRIPTION' => $items['SHORTDESCRIPTION'],
                                'VALUE' => $items['VALUE'],
                                'ISDISCOUNT' => $items['ISDISCOUNT'],
                                'ISPERCENT' => $items['ISPERCENT'],
                                'ACTIVE' => $items['ACTIVE'],
                                'EMPNO' => $items['EMPNO'],
                                'WITHDETAILS' => $items['WITHDETAILS'],
                                'DISCTYPE' => $items['DISCTYPE'],
                                'POSTYPE' => $items['POSTYPE'],
                                'ISEXCLUSIVE' => $items['ISEXCLUSIVE'],
                                'BRANCHID' => $items['BRANCHID'],
                                'ISALLPRODUCTS' => $items['ISALLPRODUCTS'],
                                'MAXCOUNT' => $items['MAXCOUNT'],
                                'OUTLETID' => $items['OUTLETID'],
                                'PRODUCTTYPE' => $items['PRODUCTTYPE'],
                                'EMPBARCODE' => $items['EMPBARCODE']
                            ]);


                            $resProducts[] = [
                                'StatusCode' => 200,
                                'Message' => 'Success',
                                'DISCID' => $items['ID'] ?? $maxId
                            ];
                        }
                    } catch (QueryException $e) {
                        $resProducts[] = [
                            'StatusCode' => 500,
                            'Message' => 'Failed',
                            'DISCID' => $items['ID'] ?? $maxId,
                            'Details' => $e
                        ];
                    }
                }
                return response()->json([
                    'StatusCode' => 200,
                    "Message" => "Success",
                    "Info" => $resProducts
                ]);
            } else {
                return response()->json([
                    'StatusCode' => 401,
                    'Message' => 'Unauthorized access'
                ], 401);
            }
        }
    }
}
