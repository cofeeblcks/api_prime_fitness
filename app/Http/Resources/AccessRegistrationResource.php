<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccessRegistrationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'access' => new AccessControlResource($this->resource['access']),
            'member' => new UserSummaryResource($this->resource['member']),
            'subscription' => $this->resource['subscription']
                ? new SuscriptionSummaryResource($this->resource['subscription'])
                : null,
        ];
    }
}
