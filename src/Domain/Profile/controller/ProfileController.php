<?php

namespace App\Domain\Profile\controller;

use App\Application\Common\service\TokenService;
use App\Domain\Common\controller\ActionBasedController;
use App\Domain\Profile\entities\Profile;
use App\Domain\Profile\models\ProfileCreateRequest;
use App\Domain\Profile\models\ProfileGetByIdRequest;
use App\Domain\Profile\models\ProfileGetListRequest;
use App\Domain\Profile\service\ProfileService;
use App\Domain\User\service\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class ProfileController extends ActionBasedController
{
    private ProfileService $profileService;
    private UserService $userService;
    private TokenService $tokenService;

    public function __construct(
        LoggerInterface $logger,
        ProfileService $profileService,
        TokenService $tokenService,
        UserService $userService
    ) {
        $this->profileService = $profileService;
        $this->tokenService = $tokenService;
        $this->userService = $userService;
        parent::__construct($logger);
    }

    // TODO : Implement
    public function getUserProfile($userIdx): array
    {
        return [];
    }

    public function createUserProfile(Request $request, Response $response, array $args): Response
    {
        $requestDto = new ProfileCreateRequest($request);
        $profile = $this->profileService->createUserProfileByRequestDto($requestDto);
        return $this->respondWithData(
            $response,
            $profile->toArray(),
            200
        );
    }

    public function getProfile(Request $request, Response $response, array $args): Response
    {
        $requestDto = new ProfileGetByIdRequest($request, $args);

        /** @var Profile $profile */
        $profile = $this->profileService->getUserProfilesByRequest($requestDto);
        var_dump($profile);
        return $this->respondWithData(
            $response,
            $profile->toArray(),
            200
        );
    }

    public function getProfiles(Request $request, Response $response, array $args): Response
    {
        $requestDto = new ProfileGetListRequest($request);
        $claim = $this->tokenService->getClaimFromToken($requestDto->getToken());
        $user = $this->userService->getUserByUserId($claim->userId);
        $profiles = $this->profileService->getUserProfiles($user->getUid());

        $result = [];
        for($i=0; $i<count($profiles); $i++) {
            /** @var Profile $profile */
            $profile = $profiles[$i];
            $result[] = $profile->toArray();
        }

        return $this->respondWithData(
            $response,
            $result,
            200
        );
    }
}
