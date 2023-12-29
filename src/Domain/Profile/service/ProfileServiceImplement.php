<?php

namespace App\Domain\Profile\service;

use App\Application\Middleware\JwtHandler;
use App\Domain\Profile\entities\Profile;
use App\Domain\Profile\models\ProfileCreateRequest;
use App\Domain\Profile\Repository\ProfileRepository;
use App\Domain\User\repository\UserRepository;
use App\Domain\User\service\UserService;
use Cassandra\Uuid;

class ProfileServiceImplement implements ProfileService
{
    private ProfileRepository $profileRepository;
    private UserRepository $userRepository;
    private JwtHandler $jwtHandler;

    public function __construct(
        ProfileRepository $profileRepository,
        JwtHandler $jwtHandler,
        UserRepository $userRepository
    ) {
        $this->profileRepository = $profileRepository;
        $this->jwtHandler = $jwtHandler;
        $this->userRepository = $userRepository;
    }

    public function getUserProfiles(int $userIdx): array
    {
        return [];
    }

    public function createUserProfile(int $userIdx, string $nickName) : int
    {
        // TODO: Implement createUserProfile() method.
        $profile = new Profile();
        $profile->setUserIdx($userIdx);
        $profile->setProfileNickName($nickName);
        $profile->setIsPrimary(false);
        $profile->setDeleted(false);
        $profile->setActivated(true);
        $profile->setBanned(false);

        $strNow = date("Y-m-d H:i:s");
        $profile->setCreatedAt($strNow);
        $profile->setUpdatedAt($strNow);

        return $this->profileRepository->createUserProfile($userIdx, $profile);
    }

    public function checkNickNameDuplicate(string $nickName): bool
    {
        // TODO: Implement checkNickNameDuplicate() method.
        return false;
    }

    public function createUserProfileByRequestDto(ProfileCreateRequest $requestBody): ?Profile
    {
        // TODO: Implement createUserProfileByToken() method.
        $claims = $this->jwtHandler->decodeJwt($requestBody->getToken());
        $userId = $this->jwtHandler->getUserIdFromClaims($claims);

//        $user = $this->userService

        return null;
    }
}