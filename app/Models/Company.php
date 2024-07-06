<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'address',
        'phone',
        'email',
        'website',
        'facebook',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function bank_accounts()
    {
        return $this->hasMany(BankAccount::class);
    }
    // public function bank_accounts()
    // {
    //     return $this->morphMany(BankAccount::class, 'bank_accountable');
    // }
}
