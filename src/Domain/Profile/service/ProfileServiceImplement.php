<?php

namespace App\Domain\Profile\service;

use App\Application\Common\MemberPasswordEncrypt;
use App\Application\Common\model\JwtClaim;
use App\Application\Common\service\TokenService;
use App\Application\Middleware\JwtHandler;
use App\Domain\Profile\entities\Profile;
use App\Domain\Profile\models\ProfileCreateRequest;
use App\Domain\Profile\models\ProfileGetByIdRequest;
use App\Domain\Profile\models\SearchByNicknameRequest;
use App\Domain\Profile\Repository\ProfileRepository;
use App\Domain\User\entities\User;
use App\Domain\User\repository\UserRepository;
use App\Domain\User\service\UserService;
use Cassandra\Uuid;
use HttpException;
use Exception;

class ProfileServiceImplement implements ProfileService
{
    private ProfileRepository $profileRepository;
    private UserRepository $userRepository;
    private JwtHandler $jwtHandler;
    private MemberPasswordEncrypt $encrypt;
    private TokenService $tokenService;

    public function __construct(
        ProfileRepository $profileRepository,
        JwtHandler $jwtHandler,
        UserRepository $userRepository,
        TokenService $tokenService,
        MemberPasswordEncrypt $encrypt
    ) {
        $this->profileRepository = $profileRepository;
        $this->jwtHandler = $jwtHandler;
        $this->userRepository = $userRepository;
        $this->tokenService = $tokenService;
        $this->encrypt = $encrypt;
    }

    public function getUserProfiles(string $uid): array
    {
        return $this->profileRepository->getUserProfileByUserIdxAndActivate($uid, true);
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
        return $this->profileRepository->checkNicknameCount($nickName) > 0;
    }

    /**
     * @throws \HttpException
     * @throws Exception
     */
    public function createUserProfileByRequestDto(ProfileCreateRequest $requestBody): ?Profile
    {
        /*
            var_dump($requestBody);
            echo "Before Decoded Token : " . $requestBody->getToken() . PHP_EOL;
        */
        $decodeToken = $this->encrypt->decrypt($requestBody->getToken());

        /*
            echo "Decoded Token : " . $decodeToken . PHP_EOL;
        */
        $decodedJwt = $this->jwtHandler->decryptToken($decodeToken);
        $claims = $this->jwtHandler->decodeJwt($decodedJwt);

        $userId = $claims->userId;

        $user = $this->userRepository->findUserOfUserId($userId);

        if (is_null($user)) {
            throw new Exception("user not found", 403);
        }

        $userUid = $user->getUid();
        $userIdx = $user->getIdx();
        $nickName = $requestBody->getNickname();

        if($this->checkNickNameDuplicate($nickName)) {
            throw new Exception("nickname already used", 503);
        }

        $profileIdx = $this->createUserProfile(
            $userIdx,
            $userUid,
            $nickName
        );

        return $this->profileRepository->getUserProfileByProfileIdx($profileIdx);
    }

    public function getUserProfilesByRequest(ProfileGetByIdRequest $request) : ?Profile
    {
        $token = $request->getToken();

        /** @var JwtClaim $claims */
        $claims = $this->tokenService->getClaimFromToken($token);

        $profileUid = $claims->profileUid;
        var_dump($profileUid);
        return $this->profileRepository->getUserProfileByProfileUid($profileUid);
    }

    /**
     * @throws Exception
     */
    public function updateProfileActivation(User $user, Profile $profile, bool $activation): ?Profile
    {
        if ($user->getUid() !== $profile->getUserUid()) {
            throw new Exception("자신의 프로필만 수정 할 수 있습니다.");
        }

        return $this->profileRepository->updateProfileActivation($profile, $activation);
    }

    public function getUserProfileByProfileUid(string $uid): ?Profile
    {
        return $this->profileRepository->getUserProfileByProfileUid($uid);
    }

    public function searchByNickname(SearchByNicknameRequest $requestDto): array
    {
        return $this->profileRepository->getListByNickname($requestDto->getNickname());
    }
}