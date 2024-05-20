<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }
        $credential = $validator->validated();
        if (Auth::attempt($credential)) {
            $user = Auth::user();
            $payload = [
                'iss' => 'Laravel Bayu',
                'role' => $user->role,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'iat' => now()->timestamp,
                'exp' => now()->timestamp + 3600
            ];
            $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');
            return response()->json([
                'Bearer ' => $jwt
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Password atau email anda salah',
        ], 401);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }
        $input = $validator->validated();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $payload = [
            'iss' => 'Laravel Bayu',
            'role' => $user->role,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'iat' => now()->timestamp,
            'exp' => now()->timestamp + 3600
        ];
        $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');
        return response()->json([
            'Bearer ' => $jwt
        ], 200);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $findUser = User::where('email', $user->email)->first();
            if ($findUser) {
                Auth::login($findUser);
                $payload = [
                    'iss' => 'Laravel Bayu',
                    'role' => $findUser->role,
                    'id' => $findUser->id,
                    'name' => $findUser->name,
                    'email' => $findUser->email,
                    'iat' => now()->timestamp,
                    'exp' => now()->timestamp + 3600
                ];
                $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');
                return response()->json([
                    'Bearer ' => $jwt,
                    'message' => 'Login Berhasil',
                ], 200);
            }
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => bcrypt($user->email),
            ]);
            Auth::login($newUser);
            $payload = [
                'iss' => 'Laravel Bayu',
                'role' => $newUser->role,
                'id' => $newUser->id,
                'name' => $newUser->name,
                'email' => $newUser->email,
                'iat' => now()->timestamp,
                'exp' => now()->timestamp + 3600
            ];
            $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');
            return response()->json([
                'Bearer ' => $jwt,
                'message' => 'Login dan Register Berhasil',
            ], 200);
        } catch (\Exception $e) {
            return redirect()->away('https://bayuif22a.ylladev.my.id/api/oauth/register');
        }
    }
}
