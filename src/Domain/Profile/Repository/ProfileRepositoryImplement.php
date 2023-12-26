<?php

namespace App\Domain\Profile\Repository;

use _PHPStan_4c4f22f13\Nette\Neon\Exception;
use App\Domain\Common\repository\BaseRepository;
use App\Domain\Profile\entities\Profile;
use App\Domain\Profile\Repository\ProfileRepository;
use Psr\Container\ContainerInterface;

class ProfileRepositoryImplement extends BaseRepository implements ProfileRepository
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @throws Exception
     */
    public function createUserProfile(int $userIdx, Profile $profile) : int
    {
        // TODO: Implement createUserProfile() method.
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO profile
            SET user_idx = :useIdx,
                uuid = UPPER(UUID()),
                profile_nickname = :nickName,
                is_primary = :isPrimary,
                deleted = :deleted,
                activate = :activate,
                banned = :banned,
                created_at = :createdAt,
                updated_at = :updatedAt
        ");

        $stmt->bindValue("useIdx",      $profile->getUserIdx());
        $stmt->bindValue("nickName",    $profile->getProfileNickName());
        $stmt->bindValue("isPrimary",   $profile->getIsPrimary());
        $stmt->bindValue("deleted",     $profile->getDeleted());
        $stmt->bindValue("activate",    $profile->getActivated());
        $stmt->bindValue("banned",      $profile->getBanned());
        $stmt->bindValue("createdAt",   $profile->getCreatedAt());
        $stmt->bindValue("updatedAt",   $profile->getUpdatedAt());

        $result = $stmt->execute();

        if(!$result) {
            throw new Exception("");
        }
        $lastId = $pdo->lastInsertId();
        $this->disposePdo($pdo);
        return $lastId;
    }
}