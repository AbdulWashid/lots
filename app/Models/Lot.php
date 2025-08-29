<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'number', 'description'];

    /**
     * Get the product that owns the lot.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
