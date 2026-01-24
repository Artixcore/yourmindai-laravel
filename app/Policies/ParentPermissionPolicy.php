<?php

namespace App\Policies;

use App\Models\ParentPermission;
use App\Models\User;

class ParentPermissionPolicy
{
    /**
     * Determine if the user can view any parent permissions.
     */
    public function viewAny(User $user): bool
    {
        // Only admin can view parent permissions
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can view the parent permission.
     */
    public function view(User $user, ParentPermission $permission): bool
    {
        // Only admin can view
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can create parent permissions.
     */
    public function create(User $user): bool
    {
        // Only admin can create
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can update the parent permission.
     */
    public function update(User $user, ParentPermission $permission): bool
    {
        // Only admin can update
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can delete the parent permission.
     */
    public function delete(User $user, ParentPermission $permission): bool
    {
        // Only admin can delete
        return $user->role === 'admin';
    }
}
