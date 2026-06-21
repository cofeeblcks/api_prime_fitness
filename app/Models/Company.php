<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'slogan',
    'logo',
    'address',
    'description',
])]
class Company extends Model
{
    use SoftDeletes;

    protected function logo(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Storage::url($value) : null,
        );
    }

    public function links(): HasMany
    {
        return $this->hasMany(CompanyLink::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(CompanyEmail::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(CompanyPhone::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(CompanyService::class);
    }

    public function coordinates(): HasOne
    {
        return $this->hasOne(Coordinate::class);
    }
}
