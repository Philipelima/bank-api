<?php
declare(strict_types=1);

namespace App\Services\Operation;

use App\Models\User;
use App\Enums\User\UserType;

use App\Repository\Operation\Balance\BalanceHistoryRepository;
use App\Exceptions\User\InvalidUserTypeException;
use App\Models\Operation\Balance;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DepositService 
{
    public function __construct(
        private BalanceHistoryRepository $balanceHistoryRepository,
        private NotificationService $notificationService
    ){
    }

    /**
     * @param array{amount: float} $data
     * @param User $user
     * 
     * @return Balance
     * 
     * @throws InvalidUserTypeException
     * @throws InvalidArgumentException
     */
    public function create(array $data, User $user): Balance
    {

        $amount = filter_var($data['amount'], FILTER_VALIDATE_FLOAT);
        if ($amount === false || $amount <= 0) {
            throw new \InvalidArgumentException("Amount must be a valid number greater than 0.");
        }

        $this->ensureUserCanDeposit($user);

        try {
            
            $deposit = DB::transaction(function () use ($data, $user) {

                $operation = $this->balanceHistoryRepository->last($user->uuid);

                return $this->balanceHistoryRepository->create([
                    'user_uuid'       => $user->uuid,
                    'balance'         => $operation->balance + $data['amount'],
                    'last_balance'    => $operation->balance,
                    'change_amount'   => $data['amount'], 
                    'operation_type'  => 'deposit',
                ]);
            });
    
            $this->notificationService->sendNotification($user, "We've received your deposit");
        
        } catch(Throwable $e) {

            Log::error('DB::transaction Error: ' . $e->getMessage());

            $this->notificationService->sendNotification($user, "We couldn’t process your deposit. Please check your information or try again later.");

        }

        return $deposit;
    }

    private function ensureUserCanDeposit(User $user): void
    {
        if ($user->user_type !== UserType::COMMON) {
            throw new InvalidUserTypeException("sorry, Only common users can deposit money.");
        }
    }

}