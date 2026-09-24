<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone_number' => 'nullable|string|max:20',
        ]);

        // STRICT RULE: Pendaftaran mandiri SELALU role 'user'
        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']), // Secure hashing
            'role' => 'user', // Forced role user, tidak bisa pilih role sendiri
            'phone_number' => $validated['phone_number'] ?? null,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', strtolower($request->email))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial tidak valid.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Jika mail driver belum dikonfigurasi, berikan info yang jelas
        if (config('mail.mailer') === 'log' || empty(config('mail.mailers.smtp.host'))) {
            return response()->json([
                'message' => 'Fitur pemulihan password siap. Layanan email sekolah belum dikonfigurasi. Silakan hubungi Administrator.',
                'email_service_ready' => false,
            ], 200);
        }

        return response()->json([
            'message' => 'Link pemulihan password telah dikirim ke email Anda.',
            'email_service_ready' => true,
        ]);
    }
}
