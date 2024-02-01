<?php

namespace App\Domain\Profile\models;

use App\Domain\Common\models\AuthorizationRequestTrait;
use Psr\Http\Message\RequestInterface;

class ProfilePatchActivationRequest
{
    private ?string $token;
    private ?string $profileUid;
    private ?bool $activate;

    use AuthorizationRequestTrait;

    public function __construct(RequestInterface $request, array $arg)
    {
        $this->token = $this->extractToken($request);
        $this->profileUid = $arg['uid'];
        $this->activate = json_decode($request->getBody(), true)['activate'];
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getProfileUid(): ?string
    {
        return $this->profileUid;
    }

    public function setProfileUid(?string $profileUid): void
    {
        $this->profileUid = $profileUid;
    }

    public function getActivate(): ?bool
    {
        return $this->activate;
    }

    public function setActivate(?bool $activate): void
    {
        $this->activate = $activate;
    }

}