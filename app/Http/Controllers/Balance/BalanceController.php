<?php

namespace App\Http\Controllers\Balance;

use App\Http\Controllers\Controller;
use App\Services\Balance\BalanceService;
use Illuminate\Http\Request;

class BalanceController extends Controller
{

    public function __construct(
        private BalanceService $balanceService
    ){   
    }

    /**
     * @OA\Get(
     *     path="/api/balance",
     *     summary="realiza consulta de saldo bancario.",
     *     tags={"balance"},
     *     @OA\Response(
     *         response=200,
     *         description="Status Ok"
     *     ), 
     *     @OA\Response(
     *         response=401,
     *         description="Status Unauthorized"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user    = auth()->user();

        $balance = $this->balanceService->last($user);
        
        return response()->json([
            'message' => null, 
            'data'    => [
                'balance' => $balance->balance
            ]
        ]);
    }
}
