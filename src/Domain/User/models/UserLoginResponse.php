<?php

namespace App\Domain\User\models;

use App\Domain\Common\models\BaseDto;

class UserLoginResponse extends BaseDto
{
    public array $profiles;
    public int $userIdx;
    public string $token;

    public function __construct($params = null)
    {
        parent::__construct($params);
    }

    public function getProfiles(): array
    {
        return $this->profiles;
    }

    public function setProfiles(array $profiles): void
    {
        $this->profiles = $profiles;
    }

    public function getUserIdx(): int
    {
        return $this->userIdx;
    }

    public function setUserIdx(int $userIdx): void
    {
        $this->userIdx = $userIdx;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }


}