<?php

namespace App\Models\Operation\Transfer;

use App\Enums\Transfer\TransferStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Transfer extends Model
{

    use HasFactory, Notifiable, HasUuids;
    
    protected $primaryKey = 'uuid'; // Define a chave primária como 'uuid'
    
    public $incrementing  = false; // Impede que o Laravel trate como autoincremento
    
    protected $keyType    = 'string'; // Define o tipo da chave primária como string

    protected $table      = 'transfers';

    protected $fillable   = [
        'uuid', 
        'payer_uuid', 
        'payee_uuid', 
        'amount',
        'status',
        'authorization_code',
        'authorized_at',
        'completed_at',
        'failed_reason', 
        'created_at', 
        'updated_at'
    ];

    
    
    public function uniqueIds()
    {
        return ['uuid'];
    }

        /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'user_type' => TransferStatus::class
        ];
    }
}
