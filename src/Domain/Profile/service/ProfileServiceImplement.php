<?php

namespace App\Domain\Profile\service;

use App\Application\Common\MemberPasswordEncrypt;
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
    private MemberPasswordEncrypt $encrypt;

    public function __construct(
        ProfileRepository $profileRepository,
        JwtHandler $jwtHandler,
        UserRepository $userRepository,
        MemberPasswordEncrypt $encrypt
    ) {
        $this->profileRepository = $profileRepository;
        $this->jwtHandler = $jwtHandler;
        $this->userRepository = $userRepository;
        $this->encrypt = $encrypt;
    }

    public function getUserProfiles(int $userIdx): array
    {
        return [];
    }

    public function createUserProfile(int $userIdx, string $userUid, string $nickName) : int
    {
        // TODO: Implement createUserProfile() method.
        $profile = new Profile();
        $profile->setUserUid($userUid);
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

    /**
     * @throws \HttpException
     */
    public function createUserProfileByRequestDto(ProfileCreateRequest $requestBody): ?Profile
    {
        // TODO: Implement createUserProfileByToken() method.
        var_dump($requestBody);
        echo "Before Decoded Token : " . $requestBody->getToken() . PHP_EOL;
        $decodeToken = $this->encrypt->decrypt($requestBody->getToken());
        echo "Decoded Token : " . $decodeToken . PHP_EOL;
        $decodedJwt = $this->jwtHandler->decryptToken($decodeToken);
        $claims = $this->jwtHandler->decodeJwt($decodedJwt);

        $userId = $this->jwtHandler->getUserIdFromClaims($claims);

        $user = $this->userRepository->findUserOfUserId($userId);

        if (is_null($user)) {
            throw new \HttpException("user not found", 403);
        }

        $userUid = $user->getUid();
        $userIdx = $user->getIdx();
        $nickName = $requestBody->getNickname();

        if($this->checkNickNameDuplicate($nickName)) {
            throw new \HttpException("nickname already used", 503);
        }

        $profileIdx = $this->createUserProfile($userIdx, $userUid, $nickName);
        $profile = $this->profileRepository->getUserProfileByProfileIdx($profileIdx);

        return $profile;
    }
}