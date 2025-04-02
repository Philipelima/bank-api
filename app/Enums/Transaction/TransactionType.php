<?php
namespace App\Enums\Transaction;

enum TransactionType: string {
    case PENDING   = 'pending';
    case COMPLETED = 'completed';
    case FAILED    = 'failed';
    case CANCELED  = 'canceled';
}