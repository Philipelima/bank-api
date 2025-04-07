<?php 
declare(strict_types=1);

namespace App\Exceptions\Transfer;

class InsufficientBalanceException extends TransactionException
{
    public function __construct(string $message, int $code = 0, \Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}