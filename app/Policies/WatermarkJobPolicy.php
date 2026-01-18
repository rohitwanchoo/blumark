<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WatermarkJob;

class WatermarkJobPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, WatermarkJob $job): bool
    {
        return $user->id === $job->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, WatermarkJob $job): bool
    {
        return $user->id === $job->user_id;
    }

    public function delete(User $user, WatermarkJob $job): bool
    {
        return $user->id === $job->user_id;
    }
}
