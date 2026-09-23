<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Grant all permissions to Admins upfront.
     */
    public function before(User $authUser, string $ability): ?bool
    {
        if ($authUser->role === 'Admin') {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the user index.
     */
    public function viewAny(User $authUser): bool
    {
        return in_array($authUser->role, ['President', 'Coordinator']);
    }

    /**
     * Determine whether the user can create another user.
     */
    public function create(User $authUser): bool
    {
        return in_array($authUser->role, ['President', 'Coordinator']);
    }

    /**
     * Determine whether the user can update a user.
     */
    public function update(User $authUser, User $targetUser): bool
    {
        // Non-admins can only manage users within their own region
        return in_array($authUser->role, ['President', 'Coordinator'])
            && (int) $authUser->region_id === (int) $targetUser->region_id;
    }

    /**
     * Determine whether the user can approve user access.
     */
    public function accept(User $authUser, User $targetUser): bool
    {
        return $this->update($authUser, $targetUser);
    }

    /**
     * Determine whether the user can delete a user.
     */
    public function delete(User $authUser, User $targetUser): bool
    {
        // Prevent deleting oneself
        if ($authUser->id === $targetUser->id) {
            return false;
        }

        return $this->update($authUser, $targetUser);
    }
}