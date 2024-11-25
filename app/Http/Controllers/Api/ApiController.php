<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        $validatorData = $request->validate([
            'name' => 'required',
            'email' => ['required', 'email'],
            'password' => ['min:8', 'confirmed'],
        ]);
        try {
            $user = User::create($validatorData);
            $token = $user->createToken("auth_token")->accessToken;
            
            
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $validatorData = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['confirmed'],
        ]);
        $user = User::where(['email' => $validatorData['email'], 'password' => $validatorData['password']]);
        $token = $user->createToken("auth_token")->accessToken;
        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Logged in  Successfully',
            'status' => 1
        ]);
    }

    public function getUser($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json([
                'user' => null,
                'message' => 'User not found',
                'status' => 0
            ]);
        } else {
            return response()->json([
                'user' => $user,
                'message' => 'User found',
                'status' => 1
            ]);
        }
    }
}
