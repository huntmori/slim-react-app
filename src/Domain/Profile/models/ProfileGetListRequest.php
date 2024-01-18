<?php

namespace App\Domain\Profile\models;

use App\Domain\Common\models\AuthorizationRequestTrait;
use Psr\Http\Message\RequestInterface;

class ProfileGetListRequest
{
    private ?string $token;

    use AuthorizationRequestTrait;

    public function __construct(RequestInterface $request)
    {
        $this->token = $this->extractToken($request);
    }

    public function getToken() : ?string
    {
        return $this->token;
    }

    public function setToken(?string $token) : ?string
    {
        $this->token = $token;
        return $this->token;
    }


}