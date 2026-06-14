<?php

namespace App\Models;

use App\Enums\SexEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'first_name',
    'last_name',
    'email',
    'phone',
    'birthdate',
    'sex',
    'identification',
    'photo',
    'height',
    'password',
    'role_id',
    'status_id',
    'identification_type_id',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'sex' => SexEnum::class,
        ];
    }

    protected function initials(): Attribute
    {
        return Attribute::make(
            get: fn () => strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1)),
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => ucfirst($this->first_name) . ' ' . ucfirst($this->last_name),
        );
    }

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value ? Storage::url($value) : null,
        );
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function identificationType(): BelongsTo
    {
        return $this->belongsTo(IdentificationType::class);
    }

    public function qrCodes(): BelongsToMany
    {
        return $this->belongsToMany(QrCode::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function fingerprints(): BelongsToMany
    {
        return $this->belongsToMany(Finger::class)
            ->using(Fingerprint::class)
            ->withPivot('id', 'fingerprint')
            ->withTimestamps();
    }
}
