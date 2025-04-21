<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected $table = 'members';

    protected $fillable = [
        'name',
        'no_phone',
        'poin',
    ];

    // Relasi: satu member bisa punya banyak pembelian
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
