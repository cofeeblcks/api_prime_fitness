<?php

namespace App\Models;

use App\Enums\IntakeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'intake',
    'description',
    'nutrition_day_id',
])]
class NutritionIntake extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'intake' => IntakeEnum::class,
        ];
    }

    public function nutritionDay(): BelongsTo
    {
        return $this->belongsTo(NutritionDay::class);
    }
}
