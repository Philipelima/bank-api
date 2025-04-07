<?php

namespace App\Services\Operation\Transfer\Handlers;

use App\Enums\User\UserType;
use App\Exceptions\Tranfer\NotAuthorizedTransferException;
use App\Exceptions\User\InvalidUserTypeException;
use App\Services\Operation\Transfer\Contracts\TransferHandlerInterface;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\Http;

// use App\Exceptions\Transfer\InsufficientBalanceException;

class NotificationHandler implements TransferHandlerInterface
{
    private ?TransferHandlerInterface $next = null;

    public function __construct(
        private NotificationService $notificationService
    ){
    }

    public function setNext(TransferHandlerInterface $handler): TransferHandlerInterface
    {
        $this->next = $handler;
        return $handler;
    }

    public function handle(array $data): void
    {
        $payeer = $data['payeer'];
        $payee  = $data['payee'];

        $this->notificationService->sendNotification($payeer, "Your R$ {$data['value']} transfer has been successfully initiated and is now being processed.");
        $this->notificationService->sendNotification($payee, "You have received a new transfer.");
    }
}
