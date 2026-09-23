<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Optional: Grant implicit access to Admins across ALL policy methods.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'Admin') {
            return true; // Bypass all checks for Admins
        }

        return null; // Fall through to standard policy checks
    }

    /**
     * Determine whether the user can view the post.
     */
    public function view(User $user, Post $post): bool
    {
        // Public posts can be seen by anyone, or private posts only by the author
        return $post->is_published || $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        // Only the post creator can update it
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}