<?php
declare(strict_types=1);

namespace App\Repository\Operation\Balance;

use App\Models\Operation\Balance;

class BalanceHistoryRepository 
{
    public function __construct(
        private Balance $balance
    ){   
    }

    public function create(array $data): Balance
    {
        return $this->balance->create($data);
    }

    public function last(string $user_uuid): Balance
    {
        return $this->balance->where('user_uuid', $user_uuid)->orderBy('created_at', 'DESC')->first();
    }
}