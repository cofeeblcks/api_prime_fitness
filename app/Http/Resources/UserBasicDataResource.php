<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBasicDataResource extends JsonResource
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
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'identification' => $this->identification,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->full_address,
            'role' => $this->role->name,
            'callsReceived' => $this->calls_received_count,
            'statusCall' => $this->last_call_received ? $this->last_call_received->status->name : 'Pendiente',
            'usersCount' => $this->users_count,
        ];
    }
}
