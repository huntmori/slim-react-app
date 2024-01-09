<?php

namespace App\Domain\Profile\controller;

use App\Domain\Common\controller\ActionBasedController;
use App\Domain\Profile\models\ProfileCreateRequest;
use App\Domain\Profile\service\ProfileService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class ProfileController extends ActionBasedController
{
    private ProfileService $profileService;

    public function __construct(
        LoggerInterface $logger,
        ProfileService $profileService
    ) {
        $this->profileService = $profileService;
        parent::__construct($logger);
    }

    // TODO : Implement
    public function getUserProfile($userIdx) : array
    {
        return [];
    }

    public function createUserProfile(Request $request, Response $response, array $args) : Response
    {
        $requestDto = new ProfileCreateRequest($request);
        $profile = $this->profileService->createUserProfileByRequestDto($requestDto);
        return $this->respondWithData($response, $profile->toArray(), 200);
    }
}