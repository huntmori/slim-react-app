<?php

namespace App\Domain\Profile\entities;

class Profile
{
    private ?int $idx;
    private ?string $userUid;
    private ?string $uid;
    private ?string $profileNickName;
    private ?bool $isPrimary;
    private ?bool $deleted;
    private ?bool $activated;
    private ?bool $banned;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function toArray() : array {
        return ([
            'idx'=> $this->idx,
            'uid'=> $this->uid,
            'userUid' => $this->userUid,
            'profileNickName' => $this->profileNickName,
            'isPrimary' => $this->isPrimary,
            'deleted' => $this->deleted,
            'activated' => $this->activated,
            'banned' => $this->banned,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ]);
    }

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function setIdx(?int $idx): void
    {
        $this->idx = $idx;
    }

    public function getUserUid(): ?string
    {
        return $this->userUid;
    }

    public function setUserUid(?string $userUid): void
    {
        $this->userUid = $userUid;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(?string $uid): void
    {
        $this->uid = $uid;
    }

    public function getProfileNickName(): ?string
    {
        return $this->profileNickName;
    }

    public function setProfileNickName(?string $profileNickName): void
    {
        $this->profileNickName = $profileNickName;
    }

    public function getIsPrimary(): ?bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(?bool $isPrimary): void
    {
        $this->isPrimary = $isPrimary;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getActivated(): ?bool
    {
        return $this->activated;
    }

    public function setActivated(?bool $activated): void
    {
        $this->activated = $activated;
    }

    public function getBanned(): ?bool
    {
        return $this->banned;
    }

    public function setBanned(?bool $banned): void
    {
        $this->banned = $banned;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }



}