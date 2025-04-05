<?php 
declare(strict_types=1);

namespace App\Factories\Notification;


use App\Services\Notification\NotificationStrategy;
use App\Services\Notification\Strategies\HttpNotification;

class NotificationFactory
{
    public static function create(string $type): NotificationStrategy
    {
        return match ($type) {
            'http'  => new HttpNotification(),
            default => throw new \InvalidArgumentException("Invalid notification type: $type"),
        };
    }
}
