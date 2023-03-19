<?php
namespace App\PaymentMethods;

use App\Enums\PaymentMethod as PMEnum;

abstract class PaymentMethod
{
    public readonly PMEnum $paymentMethod;
    public readonly string $email;
    public readonly string $fullName;
    public readonly string $tel;

    protected float $amount;
    protected string $transactionID;
    protected array $responseErrors = [];
    protected string $responseStatus;
    protected string $responseStatusCode;
    protected string $responseMessage;

    public function GetStatusCode():int{
        return $this->responseStatusCode;
    }
    public function GetTransactionID():string{
        return $this->transactionID;
    }
    public function GetPaymentMethod():PMEnum{
        return $this->paymentMethod;
    }
    public function GetAmount():float{
        return $this->amount;
    }
    public function GetErrors():array{
        return $this->responseErrors;
    }
    public function GetStatus():string{
        return $this->responseStatus;
    }

    abstract public function Execute():bool;
}