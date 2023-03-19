<?php
namespace App\Services;

use App\Http\Requests\CheckoutRequest;
use App\Interfaces\IPaymentMethod;
use App\PaymentMethods\PMOnDelivery;
use App\PaymentMethods\PMCard;
use App\PaymentMethods\PMPaypal;
use App\Enums\PaymentMethod as PMEnum;
use App\PaymentMethods\PaymentMethod;

abstract class PaymentMethodResolver
{
    public static function ResolveFromHTTP(CheckoutRequest $request):PaymentMethod{
        $paymentMethodEnum = $request->enum('payment-method', PMEnum::class);
        $email = $request->input('email', NULL);
        $fullName = $request->input('full-name', NULL);
        $tel = $request->input('tel', NULL);
        $cartID = $request->input('cart-id', NULL);
        $cardNumber = $request->input('card-number', NULL);
        $cvv = $request->input('cvv', NULL);
        // ISSUE: When year is too high, Carbon tends to match it to a past date. 99 becomes 1999 instead of 2099.
        $cardExpirationDate = $request->has('card-expiration-date') ? $request->date('card-expiration-date', 'm/y', 'Europe/Athens') : NULL;
        // Future additions to the request payload
        // ...
        // If a new case is added to the PaymentMethod Enum and is not handled in the Match,
        // an error will be raised!
        return match($paymentMethodEnum){
            PMEnum::OnDelivery => new PMOnDelivery(PMEnum::OnDelivery, $email, $fullName, $tel, $cartID),
            PMEnum::Card => new PMCard(PMEnum::Card, $email, $fullName, $tel, $cartID, $cardNumber, $cvv, $cardExpirationDate),
            PMEnum::Paypal => new PMPaypal(PMEnum::Paypal, $email, $fullName, $tel, $cartID),
            // Future additions to payment methods
            // ...
        };
    }
}