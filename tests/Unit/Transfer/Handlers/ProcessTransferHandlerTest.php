<?php

namespace Tests\Unit\Transfer\Handlers;

use App\Models\Operation\Transfer\Transfer;
use App\Models\User;
use App\Services\Balance\BalanceService;
use App\Services\Operation\Transfer\Handlers\ProcessTransferHandler;
use PHPUnit\Framework\TestCase;

class ProcessTransferHandlerTest extends TestCase
{

    public function test_it_processes_transfer_successfully(): void
    {
        $calls = [];

        $transferMock = $this->getMockBuilder(Transfer::class)->onlyMethods(['update'])->getMock();
    
        $transferMock->method('update')->willReturnCallback(function ($data) use (&$calls) {
                $calls[] = $data;
        });
    
        $payer = new User();
        $payee = new User();
    
        $balanceServiceMock = $this->createMock(BalanceService::class);
        $balanceServiceMock->expects($this->exactly(2))->method('updateBalance');
    
        $handler = new ProcessTransferHandler($balanceServiceMock);
    
        $handler->handle([
            'transfer' => $transferMock,
            'authorization' => [
                'code' => 'ABC123',
                'authorized_at' => '2025-04-10 10:00:00'
            ],
            'payer' => $payer,
            'payee' => $payee,
            'value' => 100
        ]);
    
        $this->assertCount(2, $calls);
    
        $this->assertEquals('ABC123', $calls[0]['authorization_code']);
        $this->assertEquals('2025-04-10 10:00:00', $calls[0]['authorized_at']);
        $this->assertArrayHasKey('completed_at', $calls[1]);
    }
}
