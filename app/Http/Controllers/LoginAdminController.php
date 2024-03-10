<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginAdminController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required',
            //'device_id' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'invalid_email' => ['The provided credentials are incorrect.'],
            ]);

        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'invalid_password' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->is_admin == 0) {
            throw ValidationException::withMessages([
                'cross_login' => ['This account is intended for agents use only.'],
            ]);
        }

        //todo: finalized device security check
        // if ($user->device_id !== $request->device_id) {
        //     throw ValidationException::withMessages([
        //         'cross-login' => ['This account does not belong to this device.'],
        //     ]);
        // }

        // $user->update([
        //     'device_id' => $request->device_id,
        //     'is_active' => true,
        // ]);

        $user->tokens()->where('tokenable_id', $user->id)->delete();

        $token = $user->createToken($user->device_id)->plainTextToken;
        $data = [
            'access_token' => $token,
        ];

        return response()->json($data);

    }
}
