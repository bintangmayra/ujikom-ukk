<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';

    protected $fillable = [
        'user_id',
        'member_id',
        'total_price',
        'total_payment',
        'change',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'purchase_details')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
    // public function details()
    // {
    //     return $this->belongsToMany(Product::class, 'purchase_product')
    //                 ->withPivot('quantity', 'price')
    //                 ->withTimestamps();
    // }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function details()
{
    return $this->hasMany(PurchaseDetail::class);
}

}
