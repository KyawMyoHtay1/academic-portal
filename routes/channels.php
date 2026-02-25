<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('messages.{userId}', function (User $user, int $userId): bool {
    return (int) $user->id === (int) $userId;
});

