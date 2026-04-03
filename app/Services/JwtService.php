<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtService {
    private $key;
    private $algorithm = 'HS256';

    /**
     * @param $key
     */
    public function __construct() {
        $this->key = env('JWT_SECRET');
    }

    public function createToken($user) {
        $payload = [
            'iss' => 'uribes-place-api',
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24),// Expira en 24 horas
            'data' => $user
        ];

        return JWT::encode($payload, $this->key, $this->algorithm);
    }

    public function validateToken($token) {
        try {
            $decode = JWT::decode($token, new Key($this->key, $this->algorithm));

            return (array) $decode -> data;
        } catch(Exception $e) {
            return null;
        }
    }


}
