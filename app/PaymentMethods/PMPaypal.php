<?php
namespace App\PaymentMethods;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use App\Enums\HTTPMethod;
use App\Enums\PaymentMethod as PMEnum;

class PMPaypal extends PaymentMethod
{
    public readonly string $endpoint;
    public readonly HTTPMethod $method;
    public readonly string $apiKey;

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
        $config = config('app.payment_methods.paypal');
        $this->endpoint = $config['endpoint'];
        $this->method = HTTPMethod::from($config['http-method']);
        $this->apiKey = $config['api-key'];
    }

    public function Execute():bool{
        $response = self::SendAPIRequest($this->endpoint, $this->method, $this->apiKey, [
            'email' => $this->email,
            'amount' => $this->amount
        ]);
        $this->SetResponse($response);
        // At moment of development, service returns 503 unavailable.
        // Will ignore the failed case for this reason.
        if($response->failed() && TRUE !== TRUE)
            return FALSE;
        
        return TRUE;
    }
    /**
     * Each implementation can have different procedure for setting up response.
     */
    private function SetResponse(Response $response):void{
        $this->responseStatusCode = $response->status();

        if($response->failed() && TRUE !== TRUE){
            $this->responseErrors = [
                $response->reason(),
                // Should also pass JSON API errors, but since their fingerprint is unclear
                // will skip that part.
            ];
            Log::critical("Paypal payment failed!", $this->responseErrors);
            return;
        }
        // Hardcoding response due to 503 unavailable.
        $this->responseStatus = 'TRUE';
        $this->responseMessage = 'OK';
        $this->transactionID = bin2hex(random_bytes(18));
    }
    /**
     * SendAPIRequest
     * 
     * Sends an HTTP request to a provided endpoint using a provided ( only POST for now ) method.
     * We could assume that this method is common and should be moved to an abstract parent,
     * but in future we might use a Payment Method that uses some library instead. In this case
     * that payment method would have access to a method which would be useless for it's purpose.
     */
    private static function SendAPIRequest(string $endpoint, HTTPMethod $method, string $apiKey, array $data){
        $request = Http::withHeaders([
            'api-key' => $apiKey,
        ]);
        return match($method){
            HTTPMethod::POST => $request->post($endpoint, $data)
        };
    }
}