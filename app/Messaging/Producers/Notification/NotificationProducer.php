<?php 
declare(strict_types=1);

namespace App\Messaging\Producers\Notification;

use App\Jobs\Notification\SendNotificationJob;
use Illuminate\Support\Facades\Queue;

class NotificationProducer
{
    public static function sendToQueue(array $notificationData): void
    {
        Queue::push(new SendNotificationJob($notificationData));
    }
}