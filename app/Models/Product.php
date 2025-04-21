<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = ['name', 'stock', 'price', 'image'];

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class, 'purchase_product')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }



}
