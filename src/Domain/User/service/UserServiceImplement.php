<?php

namespace App\Domain\User\service;

use App\Application\Common\MemberPasswordEncrypt;
use App\Application\Middleware\JwtHandler;
use App\Domain\Profile\entities\Profile;
use App\Domain\Profile\service\ProfileService;
use App\Domain\User\entities\User;
use App\Domain\User\exceptions\PasswordNotMatchException;
use App\Domain\User\exceptions\UserNotFoundException;
use App\Domain\User\models\UserCreateRequest;
use App\Domain\User\models\UserLoginRequest;
use App\Domain\User\models\UserLoginResponse;
use App\Domain\User\repository\UserRepository;
use App\Domain\User\service\UserService;
use DateTime;
use Predis\Client;
use Psr\Log\LoggerInterface;
use function PHPUnit\Framework\isNull;

class UserServiceImplement implements UserService
{
    protected UserRepository $userRepository;
    private Client $redisClient;

    private MemberPasswordEncrypt $memberPasswordEncrypt;
    private JwtHandler $jwtHandler;
    private LoggerInterface $logger;
    private ProfileService $profileService;
    public function __construct(
        UserRepository $userRepository,
        Client $redisClient,
        MemberPasswordEncrypt $memberPasswordEncrypt,
        JwtHandler $jwtHandler,
        LoggerInterface $logger,
        ProfileService $profileService
    ) {
        $this->userRepository = $userRepository;
        $this->redisClient = $redisClient;
        $this->memberPasswordEncrypt = $memberPasswordEncrypt;
        $this->jwtHandler = $jwtHandler;
        $this->logger = $logger;
        $this->profileService = $profileService;
    }

    public function userCreate(UserCreateRequest $request) : User {
        return $this->userCreateParams(
            $request->id,
            $request->userName,
            $request->email,
            $request->password
        );
    }

    public function userCreateParams(
        string $id,
        string $userName,
        string $email,
        string $password
    ) : User {
        $user = new User();
        $user->set("id", $id);
        $user->set("userName", $userName);
        $user->set("email", $email);

        // TODO: password encrypt
        $plaintextPassword = $password;
        $encryptPassword = $this->memberPasswordEncrypt->encrypt($plaintextPassword);
        $user->set("password", $encryptPassword);

        $user->set('createdAt', date("Y-m-d H:i:s"));
        $user->set('updatedAt', date("Y-m-d H:i:s"));
        $user->set('deleted', false);
        // TODO: insert table
        $user->set('idx', -1);

        $userIdx = $this->userRepository->createUser($user);

        // TODO: getLastInsertedId
        try {
            $user = $this->userRepository->findUserOfIdx($userIdx);
        } catch (UserNotFoundException $e) {
            echo $e->getMessage().PHP_EOL;
        }
        return $user;
    }

    /**
     * @throws PasswordNotMatchException
     * @throws UserNotFoundException
     */
    public function userLogin(UserLoginRequest $requestBody): UserLoginResponse
    {
        $user = $this->getUserByUserId($requestBody->id);
        $this->logger->info("selected user : " . json_encode($user));
        if (is_null($user)) {
            throw new UserNotFoundException("User not found. may be invalid password or user id", 401);
        }

        $decryptPassword = $this->memberPasswordEncrypt->decrypt($user->get('password'));

        $this->logger->info('decrypt password : ' .$decryptPassword);
        $this->logger->info('request password : ' .$requestBody->password);

        if ($requestBody->password !== $decryptPassword) {
            throw new PasswordNotMatchException("User not found. may be invalid password or user id", 401);
        }

        //todo : get users profiles
        $profiles = $this->profileService->getUserProfiles($user->getUid());
        $mainProfileExist = false;
        $primaryProfile = null;
        for($i=0; $i<count($profiles); $i++) {
            /** @var Profile $profile */
            $profile = $profiles[$i];
            if($profile->getIsPrimary()) {
                $mainProfileExist = true;
                $primaryProfile = $profile;
                break;
            }
        }

        if($mainProfileExist === false) {
            for($i=0; $i<count($profiles); $i++) {
                /** @var Profile $profile */
                $profile = $profiles[$i];
                $profile->setIsPrimary(true);
                $primaryProfile = $profile;
                break;
            }
        }

        $token = $this->getUserJwtToken($user, $primaryProfile);



        return new UserLoginResponse([
            'profiles'=>$profiles,
            'userIdx' => $user->getIdx(),
            'token' => $token
        ]);
    }

    public function getUserJwtToken(User $user, ?Profile $primaryProfile) : string
    {
        $userIdx = $user->getIdx();
        $redisTokenKey = "JWT_{$userIdx}";

        $isSetRedis = $this->redisClient->get($redisTokenKey);
        $token = null;
        $tokenParameter = [];
        $tokenParameter['userId'] = $user->getId();
        $tokenParameter['profileUid'] = $primaryProfile?->getUid();
        if (is_null($isSetRedis) || true) {
            $token = $this->jwtHandler->createToken($tokenParameter);
            $token = $this->memberPasswordEncrypt->encrypt($token);
            $this->redisClient->setex($redisTokenKey, 86400, $token);
        } else {
            $token = $isSetRedis;
        }

        $this->logger->info("user {$user->getId()}'s token : {$token}");
        return $token;
    }

    public function getUserByUserId(string $id) : ?User
    {
        return $this->userRepository->findUserOfUserId($id);
    }

    public function getUserByUserIdx(int $userIdx): ?User
    {
        // TODO: Implement getUserByUserIdx() method.
        return null;
    }
}