<?php

namespace App\Models;

use App\Enums\HandEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'hand'])]
class Finger extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'hand' => HandEnum::class,
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(Fingerprint::class)
            ->withPivot('id', 'fingerprint')
            ->withTimestamps();
    }
}
