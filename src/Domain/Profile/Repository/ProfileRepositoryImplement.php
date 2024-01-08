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
            SET user_uid = :userUid,
                profile_nickname = :nickName,
                is_primary = :isPrimary,
                deleted = :deleted,
                activated = :activated,
                banned = :banned,
                created_at = :createdAt,
                updated_at = :updatedAt
        ");

        $stmt->bindValue("userUid",     $profile->getUserUid());
        $stmt->bindValue("nickName",    $profile->getProfileNickName());
        $stmt->bindValue("isPrimary",   $profile->getIsPrimary() ? 1:0);
        $stmt->bindValue("deleted",     $profile->getDeleted() ? 1:0);
        $stmt->bindValue("activated",   $profile->getActivated() ? 1:0);
        $stmt->bindValue("banned",      $profile->getBanned() ? 1 : 0);
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

    public function getUserProfileByProfileIdx(int $profileIdx)
    {
        return $this->selectOne(
            " SELECT  prf.idx                 as  idx,
                        prf.user_uid            as  userUid,
                        prf.profile_nickname    as  profileNickName,
                        prf.is_primary          as  isPrimary,
                        prf.deleted             as  deleted,
                        prf.activated           as  activated,
                        prf.banned              as  banned,
                        prf.created_at          as  createdAt,
                        prf.updated_at          as  updatedAt
                FROM    profile prf
                WHERE   prf.idx = :profileIdx
                LIMIT   1 ",
            [ 'profileIdx'=>$profileIdx ],
            Profile::class
        );
    }

    public function getUserProfileByUserUid(string $uid)
    {
        // TODO: Implement getUserProfileByUserUid() method.
        return null;
    }

    public function getUserProfileByProfileUid(string $uid)
    {
        return null;
    }
}