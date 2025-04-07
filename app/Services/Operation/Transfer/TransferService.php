<?php
declare(strict_types=1);

namespace App\Services\Operation\Transfer;

use App\Enums\Transfer\TransferStatus;
use App\Models\User;
use App\Enums\User\UserType;
use App\Exceptions\Transfer\InsufficientBalanceException;
use App\Exceptions\Transfer\NotAuthorizedTransferException;

use App\Exceptions\User\InvalidUserTypeException;
use App\Repository\Operation\Transfer\TransferRepository;

use App\Services\Notification\NotificationService;
use App\Services\Operation\Transfer\Factories\TransferChainFactory;
use App\Services\User\UserService;
use Illuminate\Support\Facades\DB;

class TransferService 
{
    public function __construct(
        private TransferChainFactory $transferChainFactory,
        private NotificationService  $notificationService,
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
        
        $this->ensureUserCanTransfer($user);
        
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

    /**
     * @param User $user 
     * 
     * @throws InvalidUserTypeException
     */ 
    private function ensureUserCanTransfer(User $user): void
    {
        if ($user->user_type !== UserType::COMMON) {
            throw new InvalidUserTypeException("sorry, only common users can transfer money.");
        }
    }

}