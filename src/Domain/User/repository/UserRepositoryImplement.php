<?php

namespace App\Domain\User\repository;

use App\Domain\Common\repository\BaseRepository;
use App\Domain\User\entities\User;
use App\Domain\User\repository\UserRepository;
use PDO;
use DateTime;
use Psr\Container\ContainerInterface;
use PDOStatement;
use function PHPUnit\Framework\isNull;

class UserRepositoryImplement extends BaseRepository implements UserRepository
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        // TODO: Implement findAll() method.
        return [];
    }

    /**
     * @inheritDoc
     */
    public function findUserOfId(int $id): User
    {
        // TODO: Implement findUserOfId() method.
        return new User();
    }

    public function createUser(User $user) : int
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO user
            SET id = :id,
                uid = upper(UUID()),
                email = :email,
                user_name = :userName,
                password = :password,
                created_at = :createdAt,
                updated_at = :updatedAt,
                deleted = 0
        ");
        $stmt->bindValue("id", $user->get('id'), PDO::PARAM_STR);
        $stmt->bindValue("email", $user->get('email'), PDO::PARAM_STR);
        $stmt->bindValue("userName", $user->get('userName'), PDO::PARAM_STR);
        $stmt->bindValue("password", $user->get('password'), PDO::PARAM_STR);

        $createdAt = $user->get('createdAt');
        $updatedAt = $user->get('updatedAt');

        $stmt->bindValue("createdAt", $createdAt, PDO::PARAM_STR);
        $stmt->bindValue("updatedAt", $updatedAt, PDO::PARAM_STR);

        $result = $stmt->execute();
        $lastInsertId = $pdo->lastInsertId();
        $this->disposePdo($pdo);
        return $lastInsertId;
    }

    public function findUserOfIdx(int $idx): User
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            SELECT  idx,
                    id,
                    uid,
                    email,
                    password,
                    created_at  as createdAt,
                    updated_at  as updatedAt,
                    deleted
            FROM    user
            WHERE   idx = :idx
        ");
        $stmt->bindValue("idx", $idx, PDO::PARAM_INT);
        $result = $stmt->execute();
        $user  = $stmt->fetchObject(User::class);
        $this->disposePdo($pdo);
        return $user;
    }

    public function findUserOfUserId(string $user): ?User
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            SELECT  idx,
                    id,
                    uid,
                    email,
                    user_name   as userName,
                    password,
                    created_at  as createdAt,
                    updated_at  as updatedAt,
                    deleted
            FROM    user
            WHERE   id = :id
        ");
        $stmt->bindValue("id", $user);
        $result = $stmt->execute();
        $user = $stmt->fetchObject(User::class);
        $this->disposePdo($pdo);
        return $user ? $user : null;
    }

    public function test (PDOStatement $statement) {
        $stmt = $this->pdo->prepare("");

        $do = function(PDOStatement $statement) {

        };
    }
}