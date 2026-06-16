<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        return response()->json(['token' => $user->createToken('api')->plainTextToken]);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->orWhere('name', $request->email)->first();

        if ($user && $user->password == $request->password) {
            return response()->json(['token' => $user->createToken('api')->plainTextToken]);
        }

        return response()->json(['errors' => ['email' => ['Неверный логин или пароль']]]);
    }

    public function logout()
    {
        return response()->json(['message' => 'ok']);
    }
}
