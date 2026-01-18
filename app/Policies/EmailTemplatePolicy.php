<?php

namespace App\Policies;

use App\Models\EmailTemplate;
use App\Models\User;

class EmailTemplatePolicy
{
    public function view(User $user, EmailTemplate $template): bool
    {
        return $user->id === $template->user_id;
    }

    public function update(User $user, EmailTemplate $template): bool
    {
        return $user->id === $template->user_id;
    }

    public function delete(User $user, EmailTemplate $template): bool
    {
        return $user->id === $template->user_id;
    }
}
