<?php

namespace App\Application\Common\service;

interface TokenService
{
    public function getUserIdFromToken(string $token);
    public function getProfileUidFromToken(string $token);
    public function getClaimFromToken(string $token);
}