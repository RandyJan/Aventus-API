<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MOP extends Model
{
    use HasFactory;
    protected $table = "mop";
    protected $fillable =[
        'ID',
        'DESC',
        'PRICELEVEL',
        'CHANGE',
        'CASHDRAW',
        'REFNO',
        'PARTIALTENDER',
        'TENDER'
    ];
}
