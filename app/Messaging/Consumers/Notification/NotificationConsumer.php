<?php 
declare(strict_types=1);

namespace App\Messaging\Consumers\Notification;


use App\Services\Notification\Strategies\HttpNotification;
use Illuminate\Support\Facades\Log;

class NotificationConsumer
{
    public function handle(array $data): void
    {
        try {
            $notifier = new HttpNotification();
            $notifier->send($data['recipient'], $data['message']);

            Log::info("Notification sent successfully", $data);
        } catch (\Exception $e) {
            Log::error("Failed to send notification", ['error' => $e->getMessage()]);
            throw $e; // Deixa a mensagem na fila para reprocessar
        }
    }
}
