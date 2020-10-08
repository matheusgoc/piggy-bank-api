<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->transaction_id,
            'key' => $this->transaction->key,
            'amount' => $this->amount,
            'type' => $this->type,
            'category' => new CategoryResource($this->category),
            'place' => $this->transaction->place,
            'description' => $this->transaction->description,
            'receipt' => $this->transaction->receipt,
            'ordered_at' => $this->transaction->ordered_at * 1000,
            'ordered_at_iso' => date('c', $this->transaction->ordered_at),
            'is_owner' => $this->is_owner,
            'currency' => $this->currency,
            'currency_exchange' => $this->currency_exchange,
        ];
    }
}
