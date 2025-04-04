<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;

use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;
use Illuminate\Queue\QueueManager;

class QueueServiceProvider extends ServiceProvider
{
    public function boot(QueueManager $queue, Dispatcher $dispatcher): void
    {
        $queue->addConnector('rabbitmq', function () use ($dispatcher) {
            return new RabbitMQConnector($dispatcher);
        });
    }
}
