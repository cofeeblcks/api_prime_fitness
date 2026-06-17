<?php

namespace App\Models;

use App\Enums\DayEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'day',
    'nutrition_id',
])]
class NutritionDay extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'day' => DayEnum::class,
        ];
    }

    public function nutrition(): BelongsTo
    {
        return $this->belongsTo(Nutrition::class);
    }

    public function intakes(): HasMany
    {
        return $this->hasMany(NutritionIntake::class);
    }
}
