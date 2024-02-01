<?php

namespace App\Application\Common\model;

use JsonSerializable;

class JwtClaim implements JsonSerializable
{
    public ?string $userId;
    public ?string $profileUid;
    public ?int $exp;

    public function init(string $userId, string $profileUid, int $exp) : JwtClaim
    {
        $this->userId = $userId;
        $this->profileUid = $profileUid;
        $this->exp = $exp;

        return $this;
    }

    public function initFromArray(array $claim) : JwtClaim
    {
        $this->init(
            $claim['userId'],
            $claim['profileUid'],
            $claim['exp']
        );

        return $this;
    }

    public function toArray() : array
    {
        return [
            'userId'=>$this->userId,
            'profileUid'=>$this->profileUid,
            'exp'=> $this->exp
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}