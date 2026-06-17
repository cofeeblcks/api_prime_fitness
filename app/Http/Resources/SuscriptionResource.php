<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'price' => $this->price,
            'user' => new UserSummaryResource($this->whenLoaded('user')),
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'type' => new SuscriptionTypeResource($this->whenLoaded('type')),
            'status' => new StatusResource($this->whenLoaded('status')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
