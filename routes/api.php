<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\CheckoutRequest;

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
        // Do stuff...
        // TMP response.
        return response()->json([], 201);
    });
});
