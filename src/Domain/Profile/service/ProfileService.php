<?php

namespace App\Domain\Profile\service;

use App\Domain\Profile\entities\Profile;
use App\Domain\Profile\models\ProfileCreateRequest;
use App\Domain\Profile\models\ProfileGetByIdRequest;

interface ProfileService
{
    public function getUserProfiles(string $uid) : array;
    public function createUserProfile(int $userIdx, string $userUid, string $nickName);

    public function checkNickNameDuplicate(string $nickName): bool;

    public function createUserProfileByRequestDto(ProfileCreateRequest $requestBody): ?Profile;

    public function getUserProfilesByRequest(ProfileGetByIdRequest $request) : ?Profile;
}