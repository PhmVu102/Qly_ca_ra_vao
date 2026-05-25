<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\Attendance;
use App\Models\WorkSchedule;
use App\Models\User;
use App\Http\Controllers\Api\ChatController;

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $user = User::where('email', $credentials['email'])->first();

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
        return response()->json([
            'message' => 'Thông tin đăng nhập không đúng',
        ], 422);
    }

    if (!$user->status || $user->is_locked) {
        return response()->json([
            'message' => 'Tài khoản không hoạt động hoặc đã bị khóa',
        ], 403);
    }

    return response()->json([
        'token_type' => 'Bearer',
        'access_token' => $user->createToken('api-token')->plainTextToken,
        'user' => $user,
    ]);
});

Route::post('/chat', [ChatController::class, 'chat']);
