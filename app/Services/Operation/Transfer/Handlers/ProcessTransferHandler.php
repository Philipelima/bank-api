<?php

namespace App\Services\Operation\Transfer\Handlers;

use App\Enums\User\UserType;
use App\Exceptions\Tranfer\NotAuthorizedTransferException;
use App\Exceptions\User\InvalidUserTypeException;
use App\Services\Operation\Transfer\Contracts\TransferHandlerInterface;
use App\Models\User;
use App\Services\Balance\BalanceService;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\Http;

// use App\Exceptions\Transfer\InsufficientBalanceException;

class ProcessTransferHandler implements TransferHandlerInterface
{
    private ?TransferHandlerInterface $next = null;

    public function __construct(
        private BalanceService $balanceService
    ){
    }

    public function setNext(TransferHandlerInterface $handler): TransferHandlerInterface
    {
        $this->next = $handler;

        return $handler;
    }

    public function handle(array $data): void
    {
        try {
            $transfer = $data['transfer'];

            $transfer->update([
                'authorization_code' => $data['authorization']['code'],
                'authorized_at'      => $data['authorization']['authorized_at']
            ]);

            $payee = $data['payee'];
            $payer = $data['payer'];

            $this->balanceService->updateBalance($payer, $data['value'], 'transfeer');
            $this->balanceService->updateBalance($payee, $data['value'], 'deposit');

            $transfer->update([
                'completed_at'  => date('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }

    }
}
