<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image'];
    public function lots()
    {
        return $this->hasMany(Lot::class);
    }

    /**
     * The "booted" method of the model.
     * Ensures that when a product is deleted, its associated lots are also deleted.
     */
    protected static function booted()
    {
        static::deleting(function ($product) {
            $product->lots()->delete();
        });
    }
}
