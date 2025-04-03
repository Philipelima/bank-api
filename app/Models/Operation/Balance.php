<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Balance extends Model
{
    use HasFactory, Notifiable, HasUuids, HasApiTokens;

    protected $primaryKey = 'uuid'; // Define a chave primária como 'uuid'
    
    public $incrementing  = false; // Impede que o Laravel trate como autoincremento
    
    protected $keyType    = 'string'; // Define o tipo da chave primária como string


    protected $table = 'balance_history';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'user_uuid',
        'balance',
        'last_balance', 
        'change_amount', 
        'operation_type',
        'created_at',
        'updated_at'
    ];

    public function uniqueIds()
    {
        return ['uuid'];
    }
}
