<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\PaymentMethod;
use Closure;
use Carbon\Carbon;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // All users are allowed to Checkout, hence no Policies are used.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email'=>'required|email:rfc,dns',
            // Needs more advanced logic for first & last names validation.
            'full-name'=>'required|string',
            // Needs more advanced logic for phone number validation.
            'tel'=>'required|numeric',
            /* Won't use the "amount" field as it creates security risk 
               (amount can be modified from the browser). Will rather use 
               reference to a Cart. 
               For cart-id we would need a DB table with carts. 
               Validation Rule would be added exists:Cart,id.
             */
            'cart-id'=>'required|int',
            'payment-method'=>[
                "required",
                new Enum(PaymentMethod::class)
            ],
            'card-number'=>[
                'required_if:payment-method,'. PaymentMethod::Card->value,
                // Needs a more advanced rule for card number verification.
                'regex:/\b(?:\d[ -]*?){13,16}\b/i'
            ],
            'cvv'=>[
                'required_if:payment-method,'. PaymentMethod::Card->value,
                'integer',
                // Got to research more about CVV rules. This might be wrong.
                'digits:3'
            ],
            'card-expiration-date'=>[
                'bail',
                'required_if:payment-method,'. PaymentMethod::Card->value,
                // American cards might have month and year in reverse order. Got to research it.
                'date_format:m/y',
                // Expired Cards should not be accepted.
                function(string $attribute, mixed $value, Closure $fail){
                    $my = explode('/', $value);
                    $y = end($my);
                    $currentYear = now()->format('y');
                    if($y < $currentYear)
                        $fail("Invalid expiration date, can't accept expired cards!");
                }
            ],
        ];
    }
}
