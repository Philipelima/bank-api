<?php 
declare(strict_types=1);

namespace App\Services\Notification;

use App\Messaging\Producers\Notification\NotificationProducer;
use App\Models\User;

class NotificationService
{
    public function sendNotification(User $recipient, string $message): void
    {
        $data = [
            'recipient' => $recipient->first_name,
            'message'   => $message,
        ];

        NotificationProducer::sendToQueue($data); // Envia para o RabbitMQ
    }
}
