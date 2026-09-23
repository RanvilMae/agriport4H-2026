<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    /**
     * Pre-authorization check for Super Admins / Admins.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'Admin') {
            return true;
        }

        return null; // Fall through to standard policy checks
    }

    /**
     * Determine whether the user can view any members.
     */
    public function viewAny(User $user): bool
    {
        return $user->role !== 'Member';
    }

    /**
     * Determine whether the user can view a specific member.
     */
    public function view(User $user, Member $member): bool
    {
        if ($user->role === 'Member') {
            return $user->email === $member->email;
        }

        if (in_array($user->role, ['President', 'Coordinator'])) {
            return (int) $user->region_id === (int) $member->region_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create members.
     */
    public function create(User $user): bool
    {
        return $user->role !== 'Member';
    }

    /**
     * Determine whether the user can update a specific member.
     */
    public function update(User $user, Member $member): bool
    {
        if ($user->role === 'Member') {
            return $user->email === $member->email;
        }

        if (in_array($user->role, ['President', 'Coordinator'])) {
            return (int) $user->region_id === (int) $member->region_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete a specific member.
     */
    public function delete(User $user, Member $member): bool
    {
        if ($user->role === 'Member') {
            return false;
        }

        if (in_array($user->role, ['President', 'Coordinator'])) {
            return (int) $user->region_id === (int) $member->region_id;
        }

        return false;
    }

    /**
     * Determine whether the user can verify a member.
     */
    public function verify(User $user, Member $member): bool
    {
        if ($user->role === 'Member') {
            return false;
        }

        if (in_array($user->role, ['President', 'Coordinator'])) {
            return (int) $user->region_id === (int) $member->region_id;
        }

        return false;
    }

    /**
     * Determine whether the user can download the Agri-Resume PDF.
     */
    public function downloadAgriResume(User $user, Member $member): bool
    {
        return $this->view($user, $member);
    }
}