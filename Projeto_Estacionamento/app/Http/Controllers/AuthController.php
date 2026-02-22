<?php

namespace App\Http\Controllers;

use App\Models\Utilizador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user = Utilizador::where('email',$request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error'=>'Credenciais inválidas'],401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()?->currentAccessToken();
        if (!$token) {
            return response()->json(['error' => 'Token não encontrado'], Response::HTTP_UNAUTHORIZED);
        }

        $token->delete();
        return response()->json(['message'=>'Logout efetuado com sucesso']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

}
