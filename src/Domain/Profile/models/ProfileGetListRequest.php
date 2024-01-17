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
}