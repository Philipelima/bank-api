<?php 

namespace App\Enums\Transfer;

enum TransferType: string {
    case PERSON_TO_BUSINESS = 'P2B';
    case PERSON_TO_PERSON   = 'P2P';
}