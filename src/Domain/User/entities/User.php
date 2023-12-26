<?php

declare(strict_types=1);

namespace App\Domain\User\entities;

use DateTime;
use JsonSerializable;

class User implements JsonSerializable
{

    private ?int $idx;
    private ?string $id;
    private ?string $email;
    private ?string $userName;
    private ?string $password;
    private ?string $createdAt;
    private ?string $updatedAt;

    private bool $deleted;

    public function set($keyName, $value): void
    {
        $this->{$keyName} = $value;
    }

    public function get($keyName) {
        return $this->{$keyName};
    }

    public array $columnMapping = [
        'idx'=>'idx',
        'id'=>'id',
        'email'=>'email',
        'userName'=>'user_name',
        'password'=>'password',
        'createdAt'=>'created_at',
        'updatedAt'=>'updated_at',
        'deleted'=>'deleted'
    ];

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'idx'=>$this->idx,
            'id'=>$this->id,
            'email'=>$this->email,
            'userName'=>$this->userName
        ];
    }

    public function getIdx(): ?int
    {
        return $this->idx;
    }

    public function setIdx(?int $idx): void
    {
        $this->idx = $idx;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(?string $userName): void
    {
        $this->userName = $userName;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
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

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }
}
