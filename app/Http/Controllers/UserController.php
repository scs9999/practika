<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function user()
    {
        return response()->json(['user' => Auth::user()]);
    }
}
