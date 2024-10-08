<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parts extends Model
{
    use HasFactory;
    protected $table = "Parts";
    

    protected $fillabe = [
        'PRODUCT_ID',
        'MASTERCODE',
        'CATEGORY',
        'PRODUCTNO',
        'PRODUCTCODE',
        'PARTNO',
        'DESCRIPTION',
        'SHORTCODE',
        'QUANTITY',
        'COST',
        'NETCOST',
        'RETAIL',
        'NETRETAIL'
    ];
    public $timestamps = false;
}
