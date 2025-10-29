<?php

namespace App\Policies;

use App\Models\Poll;
use App\Models\User\User;

class PollPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAnyVotes(User $user, Poll $poll): bool
    {
        $user = auth()->user();
        return $user->id === $poll->user_id;
    }
}
