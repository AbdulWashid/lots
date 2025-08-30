<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class LotInquiry extends Model
{
    // use HasFactory;

    protected $fillable = [
        'lot_id',
        'name',
        'mobile',
        'address',
    ];
    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    /**
     * Get the product for this inquiry through the lot.
     */
    public function product(): HasOneThrough
    {
        return $this->hasOneThrough(Product::class, Lot::class, 'id', 'id', 'lot_id', 'product_id');
    }
}
