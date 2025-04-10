<?php
declare(strict_types=1);

namespace App\Services\Operation\Transfer;

use App\Enums\Transfer\TransferStatus;
use App\Models\User;
use App\Enums\User\UserType;
use App\Exceptions\Transfer\InsufficientBalanceException;
use App\Exceptions\Transfer\NotAuthorizedTransferException;

use App\Exceptions\User\InvalidUserTypeException;
use App\Models\Operation\Transfer\Transfer;
use App\Repository\Operation\Transfer\TransferRepository;
use App\Services\Balance\BalanceService;
use App\Services\Notification\NotificationService;
use App\Services\Operation\Transfer\Factories\TransferChainFactory;
use App\Services\User\UserService;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransferService 
{
    public function __construct(
        private TransferChainFactory $transferChainFactory,
        private NotificationService  $notificationService,
        private BalanceService       $balanceService,
        private TransferRepository $transferRepository,
        private UserService  $userService
    ){
    }

    /**
     * @param array{amount: float, payer: string, payee: string} $data
     * @param User $user
     * 
     * 
     * 
     * @throws InvalidUserTypeException
     * @throws InvalidArgumentException
     */
    public function create(array $data, User $user)
    {
        $amount = filter_var($data['value'], FILTER_VALIDATE_FLOAT);
        if ($amount === false || $amount <= 0) {
            throw new \InvalidArgumentException("Amount must be a valid number greater than 0.");
        }
        
        $data['payer'] = $user;

        $payee = $this->userService->find('uuid', $data['payee']);

        if (!$payee instanceof User) {
            throw new \Exception("sorry, payee not found.");
        }
        
        $data['payee'] = $payee;

        $transfer =  DB::transaction(function() use($user, $payee, $data) { 
            return $this->transferRepository->create([
                'payer_uuid' => $user->uuid, 
                'payee_uuid' => $payee->uuid, 
                'amount'     => $data['value']
            ]);
        });

        $data['transfer'] = $transfer;
            
        try {

            $chain = $this->transferChainFactory->make();    
            $chain->handle($data);

            return $transfer;
                
        } catch (InvalidUserTypeException $e) {

            $transfer->update([
                'status'        => TransferStatus::FAILED,
                'failed_reason' => $e->getMessage()
            ]);

            throw $e;
            
        } catch (InsufficientBalanceException $e) {
            
            $transfer->update([
                'status'        => TransferStatus::FAILED,
                'failed_reason' => $e->getMessage()
            ]);

            throw $e;
            
        } catch (NotAuthorizedTransferException $e) {
            
            $transfer->update([
                'status'        => TransferStatus::FAILED,
                'failed_reason' => $e->getMessage()
            ]);

            throw $e;
        }
        
    }


    public function cancel(User $user, string $uuid)
    {
        $transfer = $this->transferRepository->find($uuid);

        if (!$transfer instanceof Transfer || $transfer->payer_uuid != $user->uuid) {
            throw new NotFoundHttpException("Sorry, transfer not found.", null, 404);
        }

        $status = TransferStatus::tryFrom($transfer->status->value);

        if ($status === TransferStatus::CANCELED) {
            throw new TransferException("Sorry, this transfer has already been canceled.", 422);
        }

        $payee =  $this->userService->find('uuid', $transfer->payee_uuid);

        $this->balanceService->updateBalance($payee, (float) $transfer->amount, 'transfeer');
        $this->balanceService->updateBalance($user,  (float) $transfer->amount, 'deposit');

        $transfer->update([
            'status' => TransferStatus::CANCELED
        ]);

        return $transfer;
    }
}