<?php
namespace App\Repositories;

use App\Models\Payment;
use App\Interfaces\IPaymentMethod;
use App\PaymentMethods\PaymentMethod;

abstract class PaymentRepo
{
    public static function Create(PaymentMethod $paymentMethod):Payment|NULL{
        // If there is no transaction ID then there was probable a server error.
        // That's a bug worth investigation and we should not persist this attempt.
        if($paymentMethod->GetTransactionID() === NULL)
            return NULL;
        return Payment::Create([
            'transaction_id' => $paymentMethod->GetTransactionID(),
            'payment_method' => $paymentMethod->GetPaymentMethod()->value,
            'amount' => $paymentMethod->GetAmount(),
            'status' => $paymentMethod->GetStatus(),
            'errors' => count($paymentMethod->GetErrors()) > 0 ? $paymentMethod->GetErrors() : NULL,
        ]);
    }

    /**
     * Other actions like Retrieve, Update & Delete should be implemented too.
     */
}