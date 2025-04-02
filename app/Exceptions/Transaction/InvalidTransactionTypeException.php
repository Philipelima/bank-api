<?php 
declare(strict_types=1);

namespace App\Exceptions\Transaction;

class InvalidTransactionTypeException extends TransactionException
{
    public function __construct(string $message, int $code, \Throwable|null $previous)
    {
        parent::__construct($message, $code, $previous);
    }
}