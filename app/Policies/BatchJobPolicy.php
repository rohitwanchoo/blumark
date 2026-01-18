<?php

namespace App\Policies;

use App\Models\BatchJob;
use App\Models\User;

class BatchJobPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BatchJob $batch): bool
    {
        return $user->id === $batch->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, BatchJob $batch): bool
    {
        return $user->id === $batch->user_id;
    }

    public function delete(User $user, BatchJob $batch): bool
    {
        return $user->id === $batch->user_id;
    }
}
