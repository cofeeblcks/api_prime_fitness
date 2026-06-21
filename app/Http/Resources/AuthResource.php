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
            'photo' => $this->photo,
            'role' => new RoleResource($this->whenLoaded('role')),
            'qrCode' => $this->whenLoaded('qrCode', function () {
                $code = $this->qrCode->first();

                return $code ? new QrCodeResource($code) : null;
            }),
        ];
    }
}
