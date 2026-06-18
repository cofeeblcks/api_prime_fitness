<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'modules' => ModuleRoleResource::collection(
                $this->when(
                    $this->relationLoaded('modules'),
                    fn () => $this->modules->where('is_active', true)->sortBy('order')
                )
            ),
        ];
    }
}
