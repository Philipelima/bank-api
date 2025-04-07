<?php

namespace App\Services\Operation\Transfer\Handlers;

use App\Exceptions\Transfer\NotAuthorizedTransferException;
use App\Services\Operation\Transfer\Contracts\TransferHandlerInterface;

use Illuminate\Support\Facades\Http;

// use App\Exceptions\Transfer\InsufficientBalanceException;

class AuthorizeHandler implements TransferHandlerInterface
{
    private ?TransferHandlerInterface $next = null;

    public function __construct(
        private string $url = 'https://util.devi.tools/api/v2/authorize'
    ){
        
    }

    public function setNext(TransferHandlerInterface $handler): TransferHandlerInterface
    {
        $this->next = $handler;
        return $handler;
    }

    public function handle(array $data): void
    {
        $response = Http::get($this->url);

        if ($response->status() == 403) {
            throw new NotAuthorizedTransferException("we couldn’t authorize your transfer. Please try again later.", 403);
        }

        if ($response->status() == 200) {
            $data['authorization'] = [
                'code'          => $this->authorizationCode(),
                'authorized_at' => date('Y-m-d H:i:s')
            ];
        }

        if ($this->next) {
            $this->next->handle($data);
        }
    }

    private function authorizationCode()
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return strtoupper(substr(str_shuffle(str_repeat($pool, 5)), 0, 10));
    }
}
