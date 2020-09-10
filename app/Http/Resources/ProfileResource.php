<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'id' => $this->user_id,
            'email' => $this->user->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'state' => $this->state,
            'city' => $this->city,
            'postalcode' => $this->postalcode,
            'balance' => $this->balance,
            'target_total_savings' => $this->target_total_savings,
            'target_monthly_savings' => $this->target_monthly_savings,
        ];
    }
}
