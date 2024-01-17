<?php

namespace App\Domain\Profile\models;

use App\Domain\Common\models\AuthorizationRequestTrait;
use Psr\Http\Message\RequestInterface;

class ProfileGetByIdRequest
{
    private ?string $profileUid;
    private ?string $token;

    use AuthorizationRequestTrait;

    public function __construct(RequestInterface $request, array $arg)
    {
        $this->token = $this->extractToken($request);
        $this->profileUid = $arg['uid'];
    }

    public function getProfileUid(): ?string
    {
        return $this->profileUid;
    }

    public function setProfileUid(?string $profileUid): void
    {
        $this->profileUid = $profileUid;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }


}