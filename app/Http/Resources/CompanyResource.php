<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slogan' => $this->slogan,
            'logo' => $this->logo,
            'address' => $this->address,
            'description' => $this->description,
            'links' => CompanyLinkResource::collection($this->whenLoaded('links')),
            'emails' => CompanyEmailResource::collection($this->whenLoaded('emails')),
            'phones' => CompanyPhoneResource::collection($this->whenLoaded('phones')),
            'services' => CompanyServiceResource::collection($this->whenLoaded('services')),
            'coordinates' => new CoordinateResource($this->whenLoaded('coordinates')),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
