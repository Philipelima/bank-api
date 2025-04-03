<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Services\Operation\DepositService;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function __construct(
        private DepositService $depositService
    ){
    }

    public function store(Request $request) 
    {
        $user = auth()->user();

        $data = $request->validate([
            'amount'  => 'required|numeric',
        ]);

        $deposit =  $this->depositService->create($data, $user);

        return response()->json([
            'message' => 'Operation completed successfully!', 
            'data'    => [
                'balance' => $deposit->balance
            ]
        ]);
    }
}
