<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json([
                'message' => 'Logged in successfully', 
                'user' => Auth::user()
            ]);
        }

        return response()->json(['message' => 'Parole vai e-pasts nesakrīt'], 401);
    
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
    
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        if (!Auth::check()) {
                return response()->json(null);
            }

        
        return response()->json(Auth::user());
    }
}
