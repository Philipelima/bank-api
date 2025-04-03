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

    /**
     * @OA\Post(
     *     path="/api/operation/deposit",
     *     summary="realiza deposito de saldo.",
     *     tags={"balance"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="float", example="390.20"),
     *         )
     *     ),
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
