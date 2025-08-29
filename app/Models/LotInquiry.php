<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LotInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'mobile',
        'address',
    ];
}
