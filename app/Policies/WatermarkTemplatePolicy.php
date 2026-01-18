<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WatermarkTemplate;

class WatermarkTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, WatermarkTemplate $template): bool
    {
        return $user->id === $template->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, WatermarkTemplate $template): bool
    {
        return $user->id === $template->user_id;
    }

    public function delete(User $user, WatermarkTemplate $template): bool
    {
        return $user->id === $template->user_id;
    }
}
