<?php

namespace App\Application\Common\service;

use App\Application\Common\model\JwtClaim;

interface TokenService
{
    public function getUserIdFromToken(string $token);
    public function getProfileUidFromToken(string $token);
    public function getClaimFromToken(string $token) : ?JwtClaim;
}