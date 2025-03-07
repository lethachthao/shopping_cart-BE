<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        $email = $request->email;
        $password = $request->password;

        if(!$email || !$password){
            return response()->json(['error' => 'Vui lòng nhập đầy đủ email và password'], 422);
        };

        $status = Auth::attempt(['email' => $email, 'password' => $password]);

        if($status) {
            $token = $request->user()->createToken('auth');

            return response()->json([
                'success' => true,
                'token' => $token->plainTextToken,
                'role' => $request->user()->role,
                'message' => 'Đăng nhập thành công',
                'user' => $request->user()
            ],);
        }

        return response()->json([
            'success' => false,
            'message' => 'Sai email hoặc mật khẩu'
        ], 401);
    }

    public function profile(Request $request){
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }
}
