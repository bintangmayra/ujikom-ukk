<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
    public function product()
{
    return $this->belongsTo(Product::class);
}


}
