<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
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
            'fullName' => $this->full_name,
            'email' => $this->email,
            'role' => new RoleResource($this->role),
        ];
    }
}
