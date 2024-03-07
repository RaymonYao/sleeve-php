<?php


namespace App\Services;


use Firebase\JWT\JWT;

class JwtService
{
    public function getToken($payload)
    {
        $time = time();
        $signer = ['exp' => $time + config('ticket.jwt.expire_second')];
        return JWT::encode(array_merge($payload, $signer), $this->getSigningKey());
    }

    public function getPayLoad($jwtToken)
    {
        return JWT::decode($jwtToken, $this->getSigningKey(), ['HS256']);
    }

    private function getSigningKey()
    {
        return config('ticket.jwt.signing_key');
    }
}