<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'description',
    'icon',
    'status_id',
])]
class TrainingType extends Model
{
    use SoftDeletes;

    protected function icon(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value ? Storage::url($value) : null,
        );
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
