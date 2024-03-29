<?php

namespace App\Domain\Profile\Repository;

use App\Domain\Profile\entities\Profile;

interface  ProfileRepository
{

    public function createUserProfile(int $userIdx, Profile $profile);

    public function getUserProfileByProfileIdx(int $profileIdx);
    public function getUserProfileByUserUid(string $uid);

    public function getUserProfileByProfileUid(string $uid);

    public function getUserProfileByUserIdxAndActivate(string $userUid, bool $activated) : array;
    public function checkNicknameCount(string $nickname) : int;

    public function updateProfileActivation(Profile $profile, bool $activation) : ?Profile;

    public function getListByNickname(?string $getNickname) : array;
}