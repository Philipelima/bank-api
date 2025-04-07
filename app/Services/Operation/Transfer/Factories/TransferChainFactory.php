<?php

namespace App\Services\Operation\Transfer\Factories;

use App\Services\Operation\Transfer\Handlers\AuthorizeHandler;
use App\Services\Operation\Transfer\Handlers\ValidateBalanceHandler;
use App\Services\Operation\Transfer\Handlers\ProcessTransferHandler;
use App\Services\Operation\Transfer\Contracts\TransferHandlerInterface;
use App\Services\Operation\Transfer\Handlers\NotificationHandler;
use App\Services\Operation\Transfer\Handlers\UserCanTransferHandler;

class TransferChainFactory
{
    public function __construct(
        private UserCanTransferHandler $userCanTransferHandler,
        private ValidateBalanceHandler $validateBalanceHandler,
        private AuthorizeHandler       $authorizeHandler, 
        private ProcessTransferHandler $processTransferHandler,
        private NotificationHandler    $notificationHandler
    ) {}

    public function make(): TransferHandlerInterface
    {
        $this->userCanTransferHandler
            ->setNext($this->validateBalanceHandler)
            ->setNext($this->authorizeHandler) 
            ->setNext($this->processTransferHandler)
            ->setNext($this->notificationHandler);


        return $this->userCanTransferHandler; 
    }
}
