<?php

namespace App\Domain\Profile\service;

use App\Domain\Profile\entities\Profile;
use App\Domain\Profile\models\ProfileCreateRequest;

interface ProfileService
{
    public function getUserProfiles(int $userIdx) : array;
    public function createUserProfile(int $userIdx, string $nickName);

    public function checkNickNameDuplicate(string $nickName): bool;

    public function createUserProfileByToken(string $token, ProfileCreateRequest $requestBody): ?Profile;
}