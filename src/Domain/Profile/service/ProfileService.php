<?php

namespace App\Domain\Profile\service;

use App\Domain\Profile\entities\Profile;
use App\Domain\Profile\models\ProfileCreateRequest;
use App\Domain\Profile\models\ProfileGetByIdRequest;
use App\Domain\Profile\models\SearchByNicknameRequest;
use App\Domain\User\entities\User;

interface ProfileService
{
    public function getUserProfiles(string $uid) : array;
    public function createUserProfile(int $userIdx, string $userUid, string $nickName);

    public function checkNickNameDuplicate(string $nickName): bool;

    public function createUserProfileByRequestDto(ProfileCreateRequest $requestBody): ?Profile;

    public function getUserProfilesByRequest(ProfileGetByIdRequest $request) : ?Profile;

    public function updateProfileActivation(User $user, Profile $profile, bool $activation) : ?Profile;


    public function getUserProfileByProfileUid(string $uid) : ?Profile;

    public function searchByNickname(SearchByNicknameRequest $requestDto) : array;

}