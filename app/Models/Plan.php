<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'code',
    'name',
    'description',
    'price',
    'is_active',
])]
class Plan extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function details(): HasMany
    {
        return $this->hasMany(PlanDetail::class);
    }

    public function suscriptions(): HasMany
    {
        return $this->hasMany(Suscription::class);
    }
}
