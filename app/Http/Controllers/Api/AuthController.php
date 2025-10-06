<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in' => Carbon::now('America/Sao_Paulo')->addHours(1)->toDateTimeString()
        ]);
    }

    public function login(Request $request)
    {
         $validator = Validator::make($request->all(), [
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    $user = User::where('email', $request->email)->first();


     if (!$user) {
        return response()->json([
            'error' => 'E-mail incorreto ou não cadastrado.'
        ], 404);
    }

    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'error' => 'Senha inválida.'
        ], 401);
    }


    $token = JWTAuth::fromUser($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Carbon::now('America/Sao_Paulo')->addHours(1)->toDateTimeString()
        ]);
    }

}
