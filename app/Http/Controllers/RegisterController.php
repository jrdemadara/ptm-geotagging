<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|unique:" . User::class,
            "email" => "required|string|email|unique:" . User::class,
            "municipality" => "required|string",
            "password" => "required|string",
            "device_id" => "required|string",
        ]);

        User::create([
            "name" => Str::lower($request->input("name")),
            "email" => Str::lower($request->input("email")),
            "municipality" => Str::lower($request->input("municipality")),
            "password" => Hash::make($request->input("password")),
            "device_id" => $request->input("device_id"),
            "is_admin" => false,
            "is_active" => true,
        ]);

        $data = [
            "message" => "success",
        ];

        return response()->json($data);
    }
}
