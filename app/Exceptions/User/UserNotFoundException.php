<?php 
declare(strict_types=1);

namespace App\Exceptions\User;

class UserNotFoundException extends UserException 
{
    public function __construct(string $message, int $code, \Throwable|null $previous)
    {
        parent::__construct($message, $code, $previous);
    }
}