<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function register(Request $request) {
    // Kiểm tra dữ liệu đầu vào
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6',
        'avatar' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',
        'role' => 'nullable|string'
    ]);

    // Xử lý avatar (nếu có)
    $avatarPath = $request->hasFile('avatar') && $request->file('avatar')->isValid()
        ? $request->file('avatar')->store('avatars', 'public') // Lưu vào thư mục 'avatars' trong public storage
        : null;

    // Tạo user mới
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'avatar' => $avatarPath, // Đảm bảo avatar không bị NULL
        'role' => $request->role ?? 'user'
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Đăng ký thành công',
        'user' => $user
    ], 201);
}



}
