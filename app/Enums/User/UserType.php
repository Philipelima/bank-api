<?php 

namespace App\Enums\User;

enum UserType: string {

   case COMMON   = 'common';
   case MERCHANT = 'merchant'; 

   public function type(): string {
    return match($this) {
        UserType::COMMON    => 'Comum', 
        UserType::MERCHANT  => 'Lojista'
    };
   }
}