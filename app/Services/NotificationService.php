<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\User;

class NotificationService
{
    public static function send(User|int $user, string $title, string $body, string $type = 'system', ?string $url = null): void
    {
        $userId = $user instanceof User ? $user->id : $user;

        NotificationLog::create([
            'user_id' => $userId,
            'title'   => $title,
            'body'    => $body,
            'type'    => $type,
            'url'     => $url,
        ]);
    }
}
