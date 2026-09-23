<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    /**
     * Grant all abilities to admins automatically.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin') || $user->hasRole('admin') || strtolower($user->role ?? '') === 'admin') {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Organization $organization): bool
    {
        return (int) $user->region_id === (int) $organization->region_id;
    }

    public function viewCertification(User $user, Organization $organization): bool
    {
        return (int) $user->region_id === (int) $organization->region_id;
    }

    public function toggleVerify(User $user, Organization $organization): bool
    {
        // Non-admins reaching here will fail because `before()` returns null for them
        return false;
    }
}