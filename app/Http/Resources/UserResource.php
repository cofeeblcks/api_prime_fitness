<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'initials' => $this->initials,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'birthDate' => $this->birthdate,
            'age' => $this->age,
            'sex' => $this->sex->label(),
            'identification' => $this->identification,
            'photo' => $this->photo,
            'height' => $this->height,
            'identificationType' => new IdentificationTypeResource($this->whenLoaded('identificationType')),
            'role' => new RoleResource($this->whenLoaded('role')),
            'status' => new StatusResource($this->whenLoaded('status')),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
