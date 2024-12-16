<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Validação dos dados de entrada
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Criação do usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash da senha
        ]);

        // Autenticar o usuário após o registro
        Auth::login($user);

        return response()->json(['message' => 'Usuário registrado com sucesso!'], 201);
    }

    /**
     * Handle user login.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validação dos dados de entrada
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Tentativa de autenticação do usuário
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Recupera o usuário autenticado
            $user = Auth::user();
            return response()->json(['message' => 'Login bem-sucedido!', 'user' => $user], 200);
        }

        return response()->json(['message' => 'Credenciais inválidas.'], 401);
    }

    /**
     * Handle user logout.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logout realizado com sucesso!'], 200);
    }
}
