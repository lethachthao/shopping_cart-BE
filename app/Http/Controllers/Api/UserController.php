<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // Hiển thị danh sách người dùng
    public function index()
    {
        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email, // Dùng email làm description (hoặc tùy chỉnh)
                'password' => $user->password,
                'avatar' => $user->avatar,
                'role' => $user->role,
            ];
        });

        return response()->json($users, 200);
    }

    // Lưu người dùng mới
    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'string|min:6',
            'avatar' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',
            'role' => 'nullable|string', // cho phép role là null hoặc không có
        ]);

        // Nếu có ảnh, xử lý lưu ảnh
    $avatarPath = $request->hasFile('avatar') && $request->file('avatar')->isValid()
        ? $request->file('avatar')->store('avatars', 'public') // Lưu vào thư mục 'avatars' trong public storage
        : null;

        // Tạo mới người dùng với giá trị mặc định nếu không có trường 'avatar' và 'role'
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'avatar' => $avatarPath, // Gán mặc định nếu không có avatar
            'role' => $validatedData['role'] ?? 'user', // Gán giá trị mặc định là 'user'
            'password' => Hash::make($validatedData['password'] ?? 'defaultPassword'),
        ]);

        // Trả về response JSON
        return response()->json([
            'success' => true,
            'message' => 'User created successfully!',
            'data' => $user,
        ], 201);
    }



    // Hiển thị thông tin người dùng theo ID
    public function show($id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json($user);
        }
        return response()->json(['message' => 'User not found'], 404);
    }


    // Cập nhật thông tin người dùng
    public function update(Request $request, $id)
    {
        // Tìm người dùng theo ID
        $user = User::find($id);

        // Nếu không tìm thấy người dùng, trả về thông báo lỗi
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Xác thực dữ liệu đầu vào
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255', // Cho phép cập nhật hoặc bỏ qua trường này
            'email' => 'nullable|email|unique:users,email,' . $user->id, // Kiểm tra email đã tồn tại, nhưng bỏ qua người dùng hiện tại
            'password' => 'nullable|string|min:6', // Nếu có mật khẩu, thì phải có ít nhất 6 ký tự
            'avatar' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg', // Kiểm tra ảnh đại diện nếu có
            'role' => 'nullable|string', // Cho phép trường role là null hoặc không có
        ]);

        // Nếu có ảnh, xử lý lưu ảnh và lưu vào thư mục avatars
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            // Xóa ảnh cũ nếu có
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Lưu ảnh mới
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        } else {
            // Giữ nguyên ảnh cũ nếu không có ảnh mới
            $avatarPath = $user->avatar;
        }

        // Cập nhật thông tin người dùng
        $user->update([
            'name' => $validatedData['name'] ?? $user->name, // Cập nhật tên nếu có
            'email' => $validatedData['email'] ?? $user->email, // Cập nhật email nếu có
            'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $user->password, // Cập nhật mật khẩu nếu có
            'avatar' => $avatarPath, // Cập nhật avatar nếu có
            'role' => $validatedData['role'] ?? $user->role, // Cập nhật role nếu có
        ]);

        // Trả về response JSON
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!',
            'data' => $user,
        ], 200);
    }

    // Xóa người dùng
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        }
        return response()->json(['message' => 'User not found'], 404);
    }
}
