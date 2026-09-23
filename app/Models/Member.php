<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Section I: Identity & Demographics
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'sex',
        'civil_status',
        'dob',
        'contact_no',
        'email',
        'member_id',
        'uid',
        'verified_at', // Added to allow fillable timestamp verification

        // Section II: Address & Location Details
        'region_id',
        'province_id',
        'city_municipality',
        'district',
        'barangay',
        'zip_code',

        // Section III: Professional & Program Details
        'member_type',
        'occupation',
        'organization_id',
        'specialization',
        'hvcdp_category',
        'crops',
        'services',
        'internship',
        'scholarship',
        'lsa_level',
        'lsa_type',
        'training_course',

        // Section IV: Agri-Resume & Field Experience Details
        'highest_education',
        'degree_course',
        'school_name',
        'land_ownership',
        'farm_area',
        'is_rsbsa_registered',
        'rsbsa_no',
        'farm_equipment',
        'agri_skills',
        'certifications',
        'agri_resume_path',
        'bio_summary',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dob'                 => 'date',
            'verified_at'         => 'datetime',
            'services'            => 'array',
            'crops'               => 'array',
            'farm_equipment'      => 'array',
            'is_rsbsa_registered' => 'boolean',
        ];
    }

    /* =========================================================================
     | Accessors & Mutators
     | ========================================================================= */

    /**
     * Virtual Age Attribute
     * Usage: $member->age
     */
    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->dob ? $this->dob->age : null,
        );
    }

    /**
     * Full Name Attribute
     * Usage: $member->full_name
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(implode(' ', array_filter([
                $this->first_name,
                $this->middle_name,
                $this->last_name,
                $this->suffix,
            ]))),
        );
    }

    /* =========================================================================
     | Query Scopes
     | ========================================================================= */

    /**
     * Scope query to search members by name or email.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $sub) use ($term) {
            $sub->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('uid', 'like', "%{$term}%");
        });
    }

    /**
     * Scope query by verification status.
     */
    public function scopeVerifiedStatus(Builder $query, ?string $status): Builder
    {
        return match ($status) {
            'verified' => $query->whereNotNull('verified_at'),
            'pending'  => $query->whereNull('verified_at'),
            default    => $query,
        };
    }

    /* =========================================================================
     | Relationships
     | ========================================================================= */

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function lsaLevel(): BelongsTo
    {
        return $this->belongsTo(LsaLevel::class, 'lsa_level', 'name');
    }

    /* =========================================================================
     | Helper Methods & Utilities
     | ========================================================================= */

    public static function suffixes(): array
    {
        return ['Jr.', 'Sr.', 'II', 'III', 'IV', 'V'];
    }

    /**
     * Generate unique identification string for the regional registry.
     */
    public static function generateUid(string $regionCode): string
    {
        $year  = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        // Format: 4H - REGION - YEAR - 000 - 00001
        return sprintf(
            "4H-%s-%s-000-%05d",
            strtoupper($regionCode),
            $year,
            $count
        );
    }
}