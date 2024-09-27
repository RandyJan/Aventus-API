<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class adjustmenRateDetails extends Model
{
    use HasFactory;
    protected $table = 'AdjustmentRate';

    protected $fillable = [
        'ID',
        'ISGROSS',
        'DESCRIPTION',
        'SHORTDESCRIPTION',
        'VALUE',
        'ISDISCOUNT',
        'ISPERCENT',
        'ACTIVE',
        'EMPNO',
        'WITHDETAILS',
        'DISCTYPE',
        'POSTYPE',
        'ISEXCLUSIVE',
        'BRANCHID',
        'ISALLPRODUCTS',
        'MAXCOUNT',
        'OUTLETID',
        'PRODUCTTYPE',
        'EMPBARCODE'
    ];
    public $timestamps = false;

}
