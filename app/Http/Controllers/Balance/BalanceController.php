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

    //
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
