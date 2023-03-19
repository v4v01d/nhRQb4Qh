<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction-id' => $this->transaction_id,
            'payment-method' => $this->payment_method->value,
            'amount' => $this->amount,
            'status' => $this->status,
            'errors' => $this->errors,
            'created_at' => $this->created_at->format('h:i d/m/Y'),
            'updated_at' => $this->updated_at->format('h:i d/m/Y'),
        ];
    }
}
