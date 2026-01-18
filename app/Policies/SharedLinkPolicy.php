<?php

namespace App\Policies;

use App\Models\SharedLink;
use App\Models\User;

class SharedLinkPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SharedLink $link): bool
    {
        return $user->id === $link->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, SharedLink $link): bool
    {
        return $user->id === $link->user_id;
    }

    public function delete(User $user, SharedLink $link): bool
    {
        return $user->id === $link->user_id;
    }
}
