<?php 
declare(strict_types=1);

namespace App\Services\Notification\Strategies;

use App\Exceptions\Notification\NotificationException;
use Illuminate\Support\Facades\Http;

use App\Models\User;
use App\Services\Notification\NotificationStrategy;

class HttpNotification implements NotificationStrategy
{
    public function send(User $recipient, string $message): void
    {
        $response = Http::post('https://util.devi.tools/api/v1/notify', [
            'recipient' => $recipient->first_name,
            'message'   => $message,
        ]);

        if (!$response->successful()) {
            throw new NotificationException("Failed to send notification: " . $response->status());
        }
    }
}