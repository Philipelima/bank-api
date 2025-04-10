<?php

namespace Tests\Unit\Transfer;

use App\Services\Operation\Transfer\Factories\TransferChainFactory;
use App\Services\Operation\Transfer\Handlers\AuthorizeHandler;
use App\Services\Operation\Transfer\Handlers\NotificationHandler;
use App\Services\Operation\Transfer\Handlers\ProcessTransferHandler;
use App\Services\Operation\Transfer\Handlers\UserCanTransferHandler;
use App\Services\Operation\Transfer\Handlers\ValidateBalanceHandler;
use PHPUnit\Framework\TestCase;

class TransferChainFactoryTest extends TestCase
{

    public function test_it_builds_the_transfer_handle_chain_correctly(): void
    {

        $userCanTransferHandler = $this->createMock(UserCanTransferHandler::class);
        $validateBalanceHandler = $this->createMock(ValidateBalanceHandler::class);
        $authHandler            = $this->createMock(AuthorizeHandler::class);
        $processTransferHandler = $this->createMock(ProcessTransferHandler::class);
        $notificationHandler    = $this->createMock(NotificationHandler::class);

        $factory = new TransferChainFactory(
            $userCanTransferHandler, 
            $validateBalanceHandler, 
            $authHandler, 
            $processTransferHandler, 
            $notificationHandler
        );
        $chain = $factory->make();

        $this->assertNotNull($chain);
        $this->assertInstanceOf(UserCanTransferHandler::class, $chain);
        $this->assertTrue(method_exists($chain, 'handle'));
    }
}
