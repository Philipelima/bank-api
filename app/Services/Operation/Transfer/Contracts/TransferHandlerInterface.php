<?php 
declare(strict_types=1);

namespace App\Services\Operation\Transfer\Contracts;


interface TransferHandlerInterface 
{
    
    public function setNext(TransferHandlerInterface $handler): TransferHandlerInterface;

    public function handle(array $data): void;

}