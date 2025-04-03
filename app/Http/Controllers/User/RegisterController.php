<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\UserService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{


    public function __construct(
        private UserService $userService = new UserService
    ){
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Realiza o cadastro de usários",
     *     tags={"user"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "email", "password", "user_type", "document"},
     *             @OA\Property(property="first_name", type="string", example="Jonh"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@email.com"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="user_type", type="string", example="common"),
     *             @OA\Property(property="document",  type="string", example="417.792.460-13")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User Succefully Created"
     *     ), 
     *     @OA\Response(
     *         response=400,
     *         description="Error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {

            $data = $request->validate([
                'first_name' => ['required', 'max:100', 'string'],
                'last_name'  => ['required', 'max:100', 'string'], 
                'email'      => ['required', 'max:100', 'string'],
                'password'   => ['required', 'max:12', 'min:8', 'string'],
                'user_type'  => ['required', 'in:common,merchant', 'string'], 
                'document'   => ['required', 'max:18', 'string']
            ]);
    
            $this->userService->create($data);

            return response()->json([
                'success' => true,
                'message' => 'User Succefully Created',
                'data'    => $data
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(), 
                'data'    => null
            ], $th->getCode() ?: 400);
        }
    }
}
