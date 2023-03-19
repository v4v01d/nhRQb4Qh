<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\CheckoutRequest;
use App\Services\PaymentMethodResolver as PMResolver;
use Illuminate\Support\Facades\Log;
use App\Repositories\PaymentRepo;
use App\Http\Resources\PaymentResource;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware(['auth:sanctum'])->group(function () {
    // Normally I would work via a controller, but since checkout
    // is a single action and while there are no other api actions
    // provided, I will stick to closure approach.
    Route::post('/checkout', function(CheckoutRequest $request){
        try{
            $paymentMethod = PMResolver::ResolveFromHTTP($request);
        }catch(\Exception $e){
            Log::critical($e->getMessage());
            return response()
            ->json([
                'errors'=>[
                    'Service is temporarily unavailable. Please try again later.'
                ]
            ], 500);
        }
        $paymentMethod->Execute();
        if($paymentMethod->GetTransactionID() === NULL){
            return response()
            ->json([
                'errors'=>$paymentMethod->GetErrors(),
            ], $paymentMethod->GetStatusCode());
        }
        // Persist.
        $paymentModel = PaymentRepo::Create($paymentMethod);
        // Ideally an HTTP Resource containing the resulting Order should be returned.
        // TMP response.
        return new PaymentResource($paymentModel);
    });
});
