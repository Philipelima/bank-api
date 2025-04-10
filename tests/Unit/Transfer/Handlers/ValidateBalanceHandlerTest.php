<?php

namespace Tests\Unit\Transfer\Handlers;

use App\Exceptions\Transfer\InsufficientBalanceException;
use App\Models\Operation\Balance;
use App\Models\User;
use App\Services\Balance\BalanceService;
use App\Services\Operation\Transfer\Contracts\TransferHandlerInterface;
use App\Services\Operation\Transfer\Handlers\ValidateBalanceHandler;
use PHPUnit\Framework\TestCase;

class ValidateBalanceHandlerTest extends TestCase
{

    public function test_it_calls_next_handler_when_balance_is_sufficient(): void
    {
        $balance = new Balance;
        $balance->balance = 200.00;

        $user = new User;
        $user->uuid = "ac846625-e3a6-41a9-a54c-e6bd3d9fbe7b";

        $balanceServiceMock = $this->createMock(BalanceService::class);
        $balanceServiceMock->method('last')->with($user)->willReturn($balance);

        $nextHandlerMock = $this->createMock(TransferHandlerInterface::class);
        $nextHandlerMock->expects($this->once())->method('handle');
        
        $handler = new ValidateBalanceHandler($balanceServiceMock);
        $handler->setNext($nextHandlerMock);

        $handler->handle(['payer' => $user, 'value' => 100.00]);
    }   

    public function test_it_throws_exception_when_user_has_no_sufficient_balance(): void 
    {
        $balance = new Balance;
        $balance->balance = 40.00;

        $user = new User;
        $user->uuid = "ac846625-e3a6-41a9-a54c-e6bd3d9fbe7b";

        $balanceServiceMock = $this->createMock(BalanceService::class);
        $balanceServiceMock->method('last')->with($user)->willReturn($balance);

        $handler = new ValidateBalanceHandler($balanceServiceMock);
        
        $this->expectException(InsufficientBalanceException::class);
        

        $handler->handle(['payer' => $user, 'value' => 400.00]);
    }

    public function test_it_throws_exception_when_user_has_a_negative_balance(): void 
    {
        $balance = new Balance;
        $balance->balance = -400.00;

        $user = new User;
        $user->uuid = "ac846625-e3a6-41a9-a54c-e6bd3d9fbe7b";

        $balanceServiceMock = $this->createMock(BalanceService::class);
        $balanceServiceMock->method('last')->with($user)->willReturn($balance);

        $handler = new ValidateBalanceHandler($balanceServiceMock);
        
        $this->expectException(InsufficientBalanceException::class);

        $handler->handle(['payer' => $user, 'value' => 400.00]);
    }

    public function test_it_works_without_a_next_handler()
    {
        $balance = new Balance;
        $balance->balance = 5000.00;

        $user = new User;
        $user->uuid = "ac846625-e3a6-41a9-a54c-e6bd3d9fbe7b";

        $balanceServiceMock = $this->createMock(BalanceService::class);
        $balanceServiceMock->method('last')->with($user)->willReturn($balance);

        $handler = new ValidateBalanceHandler($balanceServiceMock);

        $handler->handle(['payer' => $user, 'value' => 400.00]);

        $this->assertTrue(true);
    }
}
