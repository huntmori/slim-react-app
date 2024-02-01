<?php

namespace App\Application\Middleware;

use App\Application\Common\model\JwtClaim;
use App\Application\Settings\SettingsInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHandler
{
    private string $encryptKey;
    private string $tokenEncryptAlgorithm = 'HS512';
    private string $jwtEncodeAlgorithm = 'HS512';
    private string $TOKEN_EXPIRE = '+1 day';

    public function __construct(SettingsInterface $settings)
    {
        $this->encryptKey = $settings->get('config')['MEMBER_PASSWORD_ENCRYPT_KEY'];
    }

    public function createToken(array $params) :string
    {
        $userId = $params['userId'];
        $profileUid = $params['profileUid'];

        $claims = (new JwtClaim())->init(
            $userId,
            $profileUid,
            strtotime($this->TOKEN_EXPIRE)
        );

        $token = $this->encodeJwt($claims);

        return $this->encryptToken($token);
    }

    public function getUserIdFromClaims(array $claims) {
        return $claims['userId'];
    }

    public function encodeJwt(JwtClaim $claims) : string
    {
        return JWT::encode(
            $claims->toArray(),
            $this->encryptKey,
            $this->jwtEncodeAlgorithm
        );
    }

    public function decodeJwt(string $token) : JwtClaim
    {
        $key = new Key($this->encryptKey, $this->jwtEncodeAlgorithm);
        $headers = new \stdClass();
        $decoded = JWT::decode($token, $key, $headers);

        return (new JwtClaim())->initFromArray((array)$decoded);
    }

    public function encryptToken(string $token) : string
    {
        return base64_encode($token);
//        return openssl_encrypt(
//            $token,
//            $this->tokenEncryptAlgorithm,
//            $this->encryptKey,
//            0,
//            $this->encryptKey
//        );
    }

    public function decryptToken(string $token) : string
    {
        return base64_decode($token);
//        return openssl_decrypt(
//            $token,
//            $this->tokenEncryptAlgorithm,
//            $this->encryptKey,
//            0,
//            $this->encryptKey
//        );
    }
}