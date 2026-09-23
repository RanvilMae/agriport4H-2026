<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'member_id',
        'name',
        'email',
        'password',
        'role',
        'region_id',
        'province_id',
        'is_accepted',
        'accepted_by',
        'accepted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Relationship to Region
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Relationship to Province
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Relationship to Member profile linked via member_id
     */
    public function member(): HasOne
    {
        return $this->hasOne(Member::class, 'member_id', 'member_id');
    }

    /**
     * Case-insensitive Role Check
     */
    public function hasRole(string $role): bool
    {
        return strtolower($this->role) === strtolower($role);
    }

    /**
     * Role Check Helpers
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isPresident(): bool
    {
        return $this->hasRole('president');
    }

    public function isCoordinator(): bool
    {
        return $this->hasRole('coordinator');
    }

    public function isMember(): bool
    {
        return $this->hasRole('member');
    }
}