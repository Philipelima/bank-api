<?php
declare(strict_types=1);

namespace App\Services\Operation;

use App\Models\User;
use App\Enums\User\UserType;

use App\Repository\Operation\Balance\BalanceHistoryRepository;
use App\Exceptions\User\InvalidUserTypeException;
use App\Models\Operation\Balance;
use Illuminate\Support\Facades\DB;

class DepositService 
{
    public function __construct(
        private BalanceHistoryRepository $balanceHistoryRepository
    ){
    }

    public function create(array $data, User $user): Balance
    {
        if ($data['amount'] <= 0) {
            throw new \Exception("Sorry, amount most be more then 0");
        }

        $this->ensureUserCanDeposit($user);

        DB::transaction(function () use ($data, $user, &$deposit) {

            $operation = $this->balanceHistoryRepository->last($user->uuid);

            $deposit   = $this->balanceHistoryRepository->create([
                'user_uuid'       => $user->uuid,
                'balance'         => $operation->balance + $data['amount'],
                'last_balance'    => $operation->balance,
                'change_amount'   => $data['amount'], 
                'operation_type'  => 'deposit',
            ]);

        });

        return $deposit;
    }

    private function ensureUserCanDeposit(User $user): void
    {
        if ($user->user_type !== UserType::COMMON) {
            throw new InvalidUserTypeException("Only common users can deposit money.");
        }
    }

}