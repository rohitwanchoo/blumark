<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCredit extends Model
{
    protected $fillable = ['user_id', 'credits'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
