<?php

namespace Tests\Unit\Transfer\Handlers;

use App\Enums\User\UserType;
use App\Exceptions\User\InvalidUserTypeException;
use App\Models\User;
use App\Services\Operation\Transfer\Contracts\TransferHandlerInterface;
use App\Services\Operation\Transfer\Handlers\UserCanTransferHandler;
use App\Services\User\UserService;
use PHPUnit\Framework\TestCase;

class UserCanTransferHandlerTest extends TestCase
{
    public function test_it_calls_next_handler_when_user_is_common(): void
    {
        $user = new User();

        $user->uuid      = "e64124d8-c20e-41b1-b4e8-a6405fb503ea"; 
        $user->user_type = UserType::COMMON;

        $userServiceMock = $this->createMock(UserService::class);
        $userServiceMock->method('find')->willReturn($user);

        $nextHandlerMock = $this->createMock(TransferHandlerInterface::class);
        $nextHandlerMock->expects($this->once())->method('handle');

        $handler = new UserCanTransferHandler($userServiceMock);
        $handler->setNext($nextHandlerMock);

        $handler->handle(['payer' => $user]);
    }

    public function test_it_throws_exception_when_user_is_merchant_type()
    {
        $this->expectException(InvalidUserTypeException::class);

        $user = new User();

        $user->uuid      = "e64124d8-c20e-41b1-b4e8-a6405fb503ea";
        $user->user_type = UserType::MERCHANT;

        $userServiceMock = $this->createMock(UserService::class);
        $userServiceMock->method('find')->willReturn($user);

        $handler = new UserCanTransferHandler($userServiceMock);
        $handler->handle(['payer' => $user]);
    }

    public function test_it_works_without_a_next_handler()
    {
        $user = new User();

        $user->uuid      = "e64124d8-c20e-41b1-b4e8-a6405fb503ea"; 
        $user->user_type = UserType::COMMON;
        
        $userServiceMock = $this->createMock(UserService::class);
        $userServiceMock->method('find')->willReturn($user);

        $handler = new UserCanTransferHandler($userServiceMock);
        $handler->handle(['payer' => $user]);

        $this->assertTrue(true);
    }
}
