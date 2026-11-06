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
        'position',
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
            'is_accepted'       => 'boolean',
            'accepted_at'       => 'datetime',
        ];
    }

    /**
     * Auto-assign a Member ID whenever a user is created
     * (registration form, seeders, admin panel, etc.).
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->member_id)) {
                $user->member_id = static::generateMemberId($user->region_id);
            }
        });
    }

    /**
     * Build the next Member ID in the format 4H-{RegionCode}-{Year}-000-000,
     * e.g. 4H-I-2026-000-001 or 4H-IV-A-2026-000-014.
     *
     * Numbering restarts at 000-001 for each region and each year.
     * Users without a region (e.g. national Admins) use the code NAT.
     *
     * Checks both the users and members tables so a new account never
     * receives an ID that already belongs to an existing member profile
     * (which would wrongly link the account to someone else's profile).
     */
    public static function generateMemberId(?int $regionId = null): string
    {
        $regionCode = $regionId
            ? Region::whereKey($regionId)->value('region_code')
            : null;

        $regionCode = strtoupper(str_replace(' ', '', $regionCode ?: 'NAT'));

        $prefix = '4H-' . $regionCode . '-' . now()->year . '-';

        $next = max(
            static::lastSequence(static::query(), $prefix),
            static::lastSequence(Member::query(), $prefix)
        ) + 1;

        // 6-digit counter shown as two groups: 000001 -> 000-001
        $digits = str_pad((string) $next, 6, '0', STR_PAD_LEFT);

        return $prefix . substr($digits, 0, 3) . '-' . substr($digits, 3);
    }

    /**
     * Highest sequence number already used with the given prefix.
     */
    protected static function lastSequence($query, string $prefix): int
    {
        $last = $query->where('member_id', 'like', $prefix . '%')
            ->orderByRaw('LENGTH(member_id) DESC')
            ->orderByDesc('member_id')
            ->value('member_id');

        if (! $last) {
            return 0;
        }

        // "000-014" -> 14
        return (int) str_replace('-', '', substr($last, strlen($prefix)));
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