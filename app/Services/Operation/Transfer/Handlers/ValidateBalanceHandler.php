<?php

namespace App\Services\Operation\Transfer\Handlers;

use App\Exceptions\Transfer\InsufficientBalanceException;
use App\Services\Operation\Transfer\Contracts\TransferHandlerInterface;
use App\Models\User;
use App\Services\Balance\BalanceService;

class ValidateBalanceHandler implements TransferHandlerInterface
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
        /** @var User $payer */
        $payer  = $data['payer'];
        $amount = $data['value'];

        $balance = $this->balanceService->last($payer);

        $currentBalance = $balance->balance ?? 0;

        if ($currentBalance < $amount) {
            throw new InsufficientBalanceException("Insufficient balance to complete this transfer.", 422);
        }

        if ($this->next) {
            $this->next->handle($data);
        }
    }
}
