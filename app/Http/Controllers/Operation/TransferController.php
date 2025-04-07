<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Services\Operation\Transfer\TransferService;
use Illuminate\Http\Request;

class TransferController extends Controller
{

    public function __construct(
        private TransferService $transferService
    )
    {
        
    }
    /**
    * @OA\Post(
    *     path="/api/transfer",
    *     summary="Realiza uma transferência de saldo.",
    *     tags={"transfer"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"value", "payer", "payee"},
    *             @OA\Property(property="value", type="number", format="float", example=390.20),
    *             @OA\Property(property="payer", type="string", example="0195fc6f-c18f-71c0-aa5f-9b127892d829"),
    *             @OA\Property(property="payee", type="string", example="01960372-4e03-7327-ba48-c00a3e5d72dc")
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Status Ok",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Transfer completed successfully"),
    *             @OA\Property(property="data", type="object",
    *                 @OA\Property(property="uuid", type="string", example="01961274-35fc-72a8-8e64-6eb376a2b28c"),
    *                 @OA\Property(property="amount", type="number", format="float", example=390.20),
    *                 @OA\Property(property="completed_at", type="string", format="date-time", example="2025-04-07 22:52:08"),
    *                 @OA\Property(property="auth_code", type="string", example="1UKJ6W1R4L"),
    *                 @OA\Property(property="authorized_at", type="string", format="date-time", example="2025-04-07 22:52:08")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthorized",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="We couldn’t authorize your transfer. Please try again later."),
    *             @OA\Property(property="data", type="object", nullable=true, example=null)
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Unprocessable Content",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Insufficient balance to complete this transfer."),
    *             @OA\Property(property="data", type="object", nullable=true, example=null)
    *         )
    *     )
    * )
    */
    public function store(Request $request) 
    {
        $user = auth()->user();

        try {
            
            $data = $request->validate([
                'value'  => 'required|numeric',
                'payer'  => 'required|uuid', 
                'payee'   => 'required|uuid', 
            ]);

            $transfer =  $this->transferService->create($data, $user);

            return response()->json([
                'message' => 'Operation completed successfully!', 
                'data'    => [
                    'uuid'   => $transfer->uuid,
                    'amount' => $transfer->amount, 
                    'completed_at'  => $transfer->completed_at, 
                    'auth_code'     => $transfer->authorization_code,
                    'authorized_at' => $transfer->authorized_at
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(), 
                'data'    => null
            ], $th->getCode());
        }
    }
}
