<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;
    protected $fillable = [
            'bank_name',
            'number',
            'company_id'
        ];

    // public function company()
    // {
    //     return $this->belongsTo(Company::class);
    // }
    public function bank_accountable()
    {
        return $this->morphTo();
    }
}
