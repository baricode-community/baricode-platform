<?php

namespace App\Policies;

use App\Models\Kanboard;
use App\Models\Auth\User;
use Illuminate\Auth\Access\Response;

class KanboardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their accessible kanboards
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Kanboard $kanboard): bool
    {
        return $kanboard->canAccess($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create kanboards
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Kanboard $kanboard): bool
    {
        return $kanboard->canManage($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kanboard $kanboard): bool
    {
        return $kanboard->isOwner($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Kanboard $kanboard): bool
    {
        return $kanboard->isOwner($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Kanboard $kanboard): bool
    {
        return $kanboard->isOwner($user);
    }

    /**
     * Determine whether the user can manage users in the kanboard.
     */
    public function manageUsers(User $user, Kanboard $kanboard): bool
    {
        return $kanboard->canManage($user);
    }

    /**
     * Determine whether the user can invite users to the kanboard.
     */
    public function inviteUsers(User $user, Kanboard $kanboard): bool
    {
        return $kanboard->canManage($user);
    }

    /**
     * Determine whether the user can remove users from the kanboard.
     */
    public function removeUsers(User $user, Kanboard $kanboard): bool
    {
        return $kanboard->canManage($user);
    }

    /**
     * Determine whether the user can change user roles in the kanboard.
     */
    public function changeUserRoles(User $user, Kanboard $kanboard): bool
    {
        return $kanboard->isOwner($user) || $kanboard->isAdmin($user);
    }

    /**
     * Determine whether the user can archive the kanboard.
     */
    public function archive(User $user, Kanboard $kanboard): bool
    {
        return $kanboard->canManage($user);
    }
}
