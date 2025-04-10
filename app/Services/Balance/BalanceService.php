<?php
declare(strict_types=1);

namespace App\Services\Balance;

use App\Models\User;

use App\Models\Operation\Balance;
use App\Repository\Operation\Balance\BalanceHistoryRepository;


class BalanceService 
{
    public function __construct(
        private BalanceHistoryRepository $balanceHistoryRepository
    ){
    }

    public function last(User $user): Balance | null
    {
        return $this->balanceHistoryRepository->last($user->uuid);
    }

    public function updateBalance(User $user, float $amount, string $type) 
    {
        $lastOperation   = $this->balanceHistoryRepository->last($user->uuid);
        $previousBalance = $lastOperation?->balance ?? 0.00;

        $newBalance =  match ($type) {

            'deposit'  => $previousBalance + $amount,
            'transfeer', 'withdraw' => $previousBalance - $amount,
            
            default => throw new \InvalidArgumentException("Invalid operation type: {$type}")
        };

        return $this->balanceHistoryRepository->create([
            'user_uuid'      => $user->uuid,
            'balance'        => $newBalance,
            'last_balance'   => $previousBalance,
            'change_amount'  => $amount,
            'operation_type' => $type,
        ]);
    } 

}