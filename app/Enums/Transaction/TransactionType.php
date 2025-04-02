<?php 

namespace App\Enums\Transaction;

enum TransactionType: string {
    case PERSON_TO_BUSINESS = 'P2B';
    case PERSON_TO_PERSON   = 'P2P';
}