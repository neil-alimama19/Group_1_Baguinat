<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function update(User $user, Event $event): bool
    {
        return $user->hasRole('Officer');
    }

    public function delete(User $user, Event $event): bool
    {
        // Officer can only delete if there are no attendance records
        return $user->hasRole('Officer') && $event->attendances()->count() === 0;
    }
}