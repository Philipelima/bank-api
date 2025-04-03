<?php
declare(strict_types=1);

namespace App\Services\Balance;

use App\Models\User;
use App\Enums\User\UserType;


use App\Exceptions\User\InvalidUserTypeException;
use App\Models\Operation\Balance;
use App\Repository\Operation\Balance\BalanceHistoryRepository;
use Illuminate\Support\Facades\DB;

class BalanceService 
{
    public function __construct(
        private BalanceHistoryRepository $balanceHistoryRepository
    ){
    }

    public function last(User $user): Balance
    {
        return $this->balanceHistoryRepository->last($user->uuid);
    }

}