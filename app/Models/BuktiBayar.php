<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiBayar extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'buktiBayar',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
