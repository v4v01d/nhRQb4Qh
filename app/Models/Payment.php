<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PaymentMethod;

class Payment extends Model
{
    protected $fillable = [ 
        'transaction_id', 
        'payment_method', 
        'amount', 
        'status', 
        'errors' 
    ];
    protected $casts = [
        'errors' => 'array',
        'payment_method' => PaymentMethod::class,
    ];
}
