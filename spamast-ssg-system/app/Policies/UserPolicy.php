<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Allow only if the user is an Admin AND is not deleting their own account
        return $user->hasRole('Admin') && $user->id !== $model->id;
    }
}