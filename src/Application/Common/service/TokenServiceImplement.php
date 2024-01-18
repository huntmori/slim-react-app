<?php

namespace App\Application\Common\service;

use App\Application\Common\MemberPasswordEncrypt;
use App\Application\Common\model\JwtClaim;
use App\Application\Middleware\JwtHandler;
use Psr\Log\LoggerInterface;

class TokenServiceImplement implements TokenService
{
    private ?MemberPasswordEncrypt $passwordEncrypt;
    private ?JwtHandler $jwtHandler;
    private ?LoggerInterface $logger;

    public function __construct(
        MemberPasswordEncrypt $passwordEncrypt,
        JwtHandler $jwtHandler,
        LoggerInterface $logger
    ) {
        $this->passwordEncrypt = $passwordEncrypt;
        $this->logger = $logger;
        $this->jwtHandler = $jwtHandler;
    }

    public function getUserIdFromToken(string $token) : string
    {
        $decodedToken = $this->passwordEncrypt->decrypt($token);
        $decryptedJwt = $this->jwtHandler->decryptToken($decodedToken);
        $claims = $this->jwtHandler->decodeJwt($decryptedJwt);

        return $claims->userId;
    }

    public function getProfileUidFromToken(string $token) : string
    {
        $decodedToken = $this->passwordEncrypt->decrypt($token);
        $decryptedJwt = $this->jwtHandler->decryptToken($decodedToken);
        $claims = $this->jwtHandler->decodeJwt($decryptedJwt);

        return $claims->profileUid;
    }

    public function getClaimFromToken(string $token) : ?JwtClaim
    {
        $decodedToken = $this->passwordEncrypt->decrypt($token);
        $decryptedJwt = $this->jwtHandler->decryptToken($decodedToken);
        return $this->jwtHandler->decodeJwt($decryptedJwt);
    }
}