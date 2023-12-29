<?php

namespace App\Domain\Profile\models;

use App\Domain\Common\models\AuthorizationRequestTrait;
use App\Domain\Common\models\BaseDto;
use Psr\Http\Message\RequestInterface;

class ProfileCreateRequest extends BaseDto
{
    private ?string $nickname;
    private ?string $token;
    use AuthorizationRequestTrait;

    public function __construct(RequestInterface $request)
    {
        parent::__construct($request->getBody());
        $this->token = $this->extractToken($request);
    }

    public function getNickname() : ?string {
        return $this->nickname;
    }

    public function setNickname(string $nickname) : void {
        $this->nickname = $nickname;
    }

    public function getToken() : ?string {
        return $this->token;
    }

    public function setToken(?string $token) : void {
        $this->token = $token;
    }
}