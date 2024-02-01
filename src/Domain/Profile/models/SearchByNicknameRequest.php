<?php

namespace App\Domain\Profile\models;

use App\Domain\Common\models\AuthorizationRequestTrait;
use App\Domain\Common\models\BaseDto;
use Psr\Http\Message\RequestInterface;

class SearchByNicknameRequest
{
    public ?string $nickname;
    private ?string $token;

    use AuthorizationRequestTrait;

    public function __construct(RequestInterface $request, array $arg)
    {
        $this->nickname = $arg['nickname'];
        echo $this->getNickname().PHP_EOL;

        $this->token = $this->extractToken($request);
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
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