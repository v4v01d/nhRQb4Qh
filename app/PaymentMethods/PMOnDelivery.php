<?php
namespace App\PaymentMethods;

use App\Interfaces\IPaymentMethod;
use App\Enums\PaymentMethod as PMEnum;

class PMOnDelivery extends PaymentMethod
{
    public function __construct(
        public readonly PMEnum $paymentMethod,
        public readonly string $email,
        public readonly string $fullName,
        public readonly string $tel,
        public readonly int $cartID,
    ){
        // Normally the Amount would be retrieved from Cart instance, but since
        // it's creation is not requested, we'll go with hardcoded value.
        $this->amount = 75.50;
    }

    public function Execute():bool{
        // Not much to do here.
        $this->SetResponse();

        return TRUE;
    }
    
    /**
     * Each implementation can have different procedure for setting up response.
     */
    private function SetResponse():void{
        $this->responseStatusCode = 201;
        $this->responseStatus = 'PENDING PAYMENT';
        $this->responseMessage = 'OK';
        $this->transactionID = bin2hex(random_bytes(18));
    }
}