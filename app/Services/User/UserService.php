<?php
declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use App\Enums\User\UserType;
use App\Exceptions\User\InvalidUserTypeException;
use App\Repository\User\UserRepository;
use App\Validators\DocumentValidator;

class UserService 
{
    public function __construct(
        private UserRepository $userRepository = new UserRepository
    ){
    }

    public function create(array $data): User
    {
        $userType = UserType::tryFrom($data['user_type']);
        if (!$userType instanceof UserType) {
            throw new InvalidUserTypeException("Sorry, the selected user type is invalid.", 400);
        }  

        if ($userType == UserType::MERCHANT && !DocumentValidator::isValidCnpj($data['document'])) {
            throw new InvalidUserTypeException("Sorry, the informed document is not valid for the user type.", 400);
        }
        if ($userType == UserType::COMMON && !DocumentValidator::isValidCpf($data['document'])) {
            throw new InvalidUserTypeException("Sorry, the informed document is not valid for user type.", 400);
        }

        return $this->userRepository->create($data);
    }

}
