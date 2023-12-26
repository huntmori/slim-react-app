<?php

declare(strict_types=1);

namespace App\Domain\User\repository;

use App\Domain\User\entities\User;
use App\Domain\User\exceptions\UserNotFoundException;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfId(int $id): User;

    /**
     * @param int $idx
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfIdx(int $idx): User;

    public function createUser(User $user);

    public function findUserOfUserId(string $user) : ?User;
}
