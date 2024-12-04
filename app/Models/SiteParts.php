<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class SiteParts extends Model
{
    use HasFactory;
    protected $table = 'SiteParts';

    protected $fillable = [
        'ARNOC',
        'PRODUCT_ID',
        'MASTERCODE',
        'BSUNITCODE',
        'GROUP',
        'CATEGORY',
        'PRODUCTNO',
        'PRODUCTCODE',
        'PARTNO',
        'DESCRIPTION',
        'EDC_CODES',
        'STANDARD',
        'SHORTCODE',
        'QUANTITY',
        'COST',
        'NETCOST',
        'RETAIL',
        'NETRETAIL',
        'CREDIT',
        'PROVINCE',
        'STATUS',
        'VAT',
        'STDCARCASSWEIGHT',
        'TIMPLADOS'

    ];
}
