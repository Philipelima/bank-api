<?php 
declare(strict_types=1);

namespace App\Exceptions\User;

use Exception;

class UserException extends Exception 
{
    public function __construct(string $message, int $code, \Throwable|null $previous)
    {
        parent::__construct($message, $code, $previous);
    }
}