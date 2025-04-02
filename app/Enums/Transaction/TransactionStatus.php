<?php
namespace App\Enums\Transaction;

enum TransactionStatus: string {
    case PENDING   = 'pending';
    case COMPLETED = 'completed';
    case FAILED    = 'failed';
    case CANCELED  = 'canceled';
}