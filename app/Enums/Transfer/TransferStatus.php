<?php
namespace App\Enums\Transfer;

enum TransferStatus: string {
    case PENDING   = 'pending';
    case COMPLETED = 'completed';
    case FAILED    = 'failed';
    case CANCELED  = 'canceled';
}