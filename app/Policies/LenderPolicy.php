<?php

namespace App\Policies;

use App\Models\Lender;
use App\Models\User;

class LenderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Lender $lender): bool
    {
        return $user->id === $lender->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Lender $lender): bool
    {
        return $user->id === $lender->user_id;
    }

    public function delete(User $user, Lender $lender): bool
    {
        return $user->id === $lender->user_id;
    }
}
