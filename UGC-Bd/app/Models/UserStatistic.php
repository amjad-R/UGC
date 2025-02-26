<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStatistic extends Model
{
    protected $fillable = [
        'user_id',
        'transactions',
        'payments',
        'profit',
        'sales',
        'transactions_change',
        'payments_change',
        'profit_change',
        'sales_change',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
