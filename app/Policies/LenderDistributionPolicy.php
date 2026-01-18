<?php

namespace App\Policies;

use App\Models\LenderDistribution;
use App\Models\User;

class LenderDistributionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LenderDistribution $distribution): bool
    {
        return $user->id === $distribution->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, LenderDistribution $distribution): bool
    {
        return $user->id === $distribution->user_id;
    }

    public function delete(User $user, LenderDistribution $distribution): bool
    {
        return $user->id === $distribution->user_id;
    }
}
