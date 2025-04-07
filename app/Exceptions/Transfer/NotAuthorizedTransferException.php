<?php 
declare(strict_types=1);

namespace App\Exceptions\Transfer;

class NotAuthorizedTransferException extends TransactionException
{
    public function __construct(string $message, int $code = 0, \Throwable|null $previous  = null)
    {
        parent::__construct($message, $code, $previous);
    }
}