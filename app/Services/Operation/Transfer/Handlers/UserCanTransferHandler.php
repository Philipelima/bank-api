<?php

namespace App\Services\Operation\Transfer\Handlers;

use App\Enums\User\UserType;
use App\Exceptions\User\InvalidUserTypeException;
use App\Services\Operation\Transfer\Contracts\TransferHandlerInterface;
use App\Models\User;
use App\Services\User\UserService;

// use App\Exceptions\Transfer\InsufficientBalanceException;

class UserCanTransferHandler implements TransferHandlerInterface
{
    private ?TransferHandlerInterface $next = null;

    public function __construct(
        private UserService $userService
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

        $payer  = $this->userService->find('uuid', $payer->uuid);

        if ($payer->user_type !== UserType::COMMON) {
            throw new InvalidUserTypeException("sorry, only common users can transfer money.");
        }

        if ($this->next) {
            $this->next->handle($data);
        }
    }
}
