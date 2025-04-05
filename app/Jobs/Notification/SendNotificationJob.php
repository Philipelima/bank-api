<?php

namespace App\Jobs\Notification;

use App\Messaging\Consumers\Notification\NotificationConsumer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries   = 5; // Número máximo de tentativas
    public $backoff = [10, 30, 60]; // Intervalo entre tentativas (em segundos)


    /**
     * Create a new job instance.
     */
    public function __construct(
        private array $data = [],
        private NotificationConsumer $notificationConsumer = new NotificationConsumer
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->notificationConsumer->handle($this->data);
    }

    public function failed(\Exception $exception)
    {
        Log::error("Job falhou: " . $exception->getMessage());
    }
}
