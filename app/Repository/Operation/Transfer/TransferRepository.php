<?php
declare(strict_types=1);

namespace App\Repository\Operation\Transfer;

use App\Models\Operation\Balance;
use App\Models\Operation\Transfer\Transfer;

class TransferRepository 
{
    public function __construct(
        private Transfer $transfer
    ){   
    }

    public function create(array $data): Transfer
    {
        return $this->transfer->create($data);
    }

    public function find(string $uuid) 
    {
        return $this->transfer->where('uuid', $uuid)->first();
    }

    
}