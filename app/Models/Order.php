<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'phone',
        'email',
        'address',
        'description',
        'quantity',
        'pricePerItem',
        'size',
        'payment',
        'deadline',
        'progres',
        'endDate',
        'shippingCost',
        'status',
        'company_id',
    ];

    public function designs()
    {
        return $this->hasMany(Design::class);
    }
    
    public function modelOrders()
    {
        return $this->hasMany(ModelOrder::class);
    }
    public function buktiBayars()
    {
        return $this->hasMany(BuktiBayar::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
