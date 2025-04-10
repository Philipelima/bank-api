<?php

namespace Tests\Feature\Transfer;

use App\Exceptions\Transfer\InsufficientBalanceException;
use App\Models\User;
use Faker\Provider\pt_BR\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TransferIntegrationTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        
        fake()->addProvider(new Person(fake()));
    }
    
    public function test_it_returns_a_successful_transfer()
    {
        $payer = $this->createActor();

        $this->addSufficientBalanceForPayer(1000.00, $payer);

        $payee = $this->createActor();

        $token = $payer->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/transfer', [
            'value' => 100.0,
            'payer' => $payer->uuid,
            'payee' => $payee->uuid
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data' => ['uuid', 'amount', 'auth_code']]);

        $response->assertJson(['message' => 'Operation completed successfully!']);
    }

    public function test_user_can_cancel_a_transfer_successfully()
    {
        $payer = $this->createActor();

        $this->addSufficientBalanceForPayer(1000.00, $payer);

        $payee = $this->createActor();

        $token = $payer->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/transfer', [
            'value' => 100.0,
            'payer' => $payer->uuid,
            'payee' => $payee->uuid
        ]);

        $transfer = $response['data']['uuid'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/transfer/{$transfer}");


        $response->assertOk();
        $response->assertJson(['message' => 'Operation completed successfully!']);

        $this->assertDatabaseHas('transfers', [
            'uuid' => $transfer,
            'status' => 'canceled'
        ]);
    }

    public function test_user_cannot_cancel_an_already_cancelled_transfer()
    {
        $payer = $this->createActor();

        $this->addSufficientBalanceForPayer(1000.00, $payer);

        $payee = $this->createActor();

        $token = $payer->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/transfer', [
            'value' => 100.0,
            'payer' => $payer->uuid,
            'payee' => $payee->uuid
        ]);

        $transfer = $response['data']['uuid'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/transfer/{$transfer}");

        $response->assertOk();
        $response->assertJson(['message' => 'Operation completed successfully!']);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/transfer/{$transfer}");

        $response->assertUnprocessable();
        $response->assertJson(['message' => 'Sorry, this transfer has already been canceled.']);
    }

    public function test_it_returns_insufficient_balance_for_transfer()
    {
        $payer = $this->createActor();
        $payee = $this->createActor();

        $token = $payer->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/transfer', [
            'value' => 100.0,
            'payer' => $payer->uuid,
            'payee' => $payee->uuid
        ]);

        $response->assertUnprocessable();
        $response->assertJson(['message' => 'Insufficient balance to complete this transfer.']);
    }


    public  function test_it_returns_only_common_users_can_transfer()
    {
        $payer = $this->createActor('merchant');

        $this->addSufficientBalanceForPayer(1000.00, $payer);

        $payee = $this->createActor();

        $token = $payer->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/transfer', [
            'value' => 100.0,
            'payer' => $payer->uuid,
            'payee' => $payee->uuid
        ]);

        $response->assertUnprocessable();
        $response->assertJson(['message' => 'sorry, only common users can transfer money.']);
    }

    // Adiciona saldo a conta do payer para que ele possa realizar a transferência. 
    private function addSufficientBalanceForPayer(float $value, User $payer)
    {
       DB::table('balance_history')->insert([
           'uuid'           => fake()->uuid(),
           'user_uuid'      => $payer->uuid,
           'balance'        => $value,
           'last_balance'   => 0,
           'change_amount'  => $value,
           'operation_type' => 'deposit'
       ]);
    }
 
    private function createActor(string $type = 'common') 
    {
        return User::factory()->create([
            'first_name' => fake()->firstName(),
            'last_name'  => fake()->lastName(),
            'document'   => fake()->cpf(false), 
            'user_type'  => $type,
            'email'      => fake()->unique()->safeEmail(),
            'password'   => Hash::make('password_')
        ]);
    }


}
