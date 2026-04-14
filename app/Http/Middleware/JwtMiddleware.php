<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token no suministrado'], 401);
        }

        try {
            $key = env('JWT_SECRET');
            $algorithm = 'HS256';

            $decoded = JWT::decode($token, new Key($key, $algorithm));
            $userId = $decoded->data->userId ?? null;

            if ($userId) {
                $user = User::where('userId', $userId)->first();

                if ($user) {
                    Auth::login($user);
                }
            }

            $request->attributes->add(['user_data' => (array) $decoded->data]);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Token inválido o expirado',
                'detalle' => $e->getMessage()
            ], 401);
        }

        return $next($request);
    }
}
