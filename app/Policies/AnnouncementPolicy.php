<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    /**
     * Perform pre-authorization checks.
     * Super Admins / Admins bypass regional restrictions.
     */
    public function before(User $user, string $ability): ?bool
    {
        if (in_array($user->role, ['Admin', 'Super Admin', 'National Admin'])) {
            return true;
        }

        return null; // Fall through to individual policy methods
    }

    /**
     * Determine whether the user can view the announcements list.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view announcements
        return true;
    }

    /**
     * Determine whether the user can create an announcement.
     */
    public function create(User $user): bool
    {
        // Regular members/guests cannot create announcements
        return in_array($user->role, ['Admin', 'President', 'Coordinator']);
    }

    /**
     * Determine whether the user can update an announcement.
     */
    public function update(User $user, Announcement $announcement): bool
    {
        // Non-Admins can only edit announcements matching their assigned region
        if (in_array($user->role, ['President', 'Coordinator'])) {
            return (int) $user->region_id === (int) $announcement->region_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete an announcement.
     */
    public function delete(User $user, Announcement $announcement): bool
    {
        // Non-Admins can only delete announcements matching their assigned region
        if (in_array($user->role, ['President', 'Coordinator'])) {
            return (int) $user->region_id === (int) $announcement->region_id;
        }

        return false;
    }
}