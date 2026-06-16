<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'event' => $this->event,
            'logName' => $this->log_name,
            'causer' => new UserResource($this->whenLoaded('causer')), // Quien hace la accion
            'subject' => new UserResource($this->whenLoaded('subject')), // A quien se le hace la accion
            'changes' => $this->changes,
            'created_at' => $this->created_at,
        ];
    }
}
