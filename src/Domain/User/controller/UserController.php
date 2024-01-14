<?php

namespace App\Domain\User\controller;

use App\Domain\Common\controller\ActionBasedController;
use App\Domain\Profile\service\ProfileService;
use App\Domain\User\models\UserCreateRequest;
use App\Domain\User\models\UserLoginRequest;
use App\Domain\User\service\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class UserController extends ActionBasedController
{
    private LoggerInterface $logger;
    private UserService $userService;
    private ProfileService $profileService;

    public function __construct(
        LoggerInterface $logger,
        UserService $userService
    ) {
        $this->logger = $logger;
        $this->userService = $userService;
    }

    public function logPath(Request $request, string $methodName) : void {
        $logMessageString = '['.$request->getMethod().']'.$request->getUri();
        $logMessageString = $methodName.'=>>'.$logMessageString;
        $this->logger->info($logMessageString);
        //echo($logMessageString);
    }

    public function createUser(Request $request, Response $response, array $args) : Response
    {
        $requestBody = $request->getBody();
        $this->logger->info("request-body: ". $requestBody);
        $jsonDecode = json_decode($requestBody, true);

        $request = new UserCreateRequest($jsonDecode);
        $user = $this->userService->userCreate($request);

        return $this->respondWithData(
            $response,
            ['idx'=>$user->get('idx')]
        );
    }

    public function getUser(Request $request, Response $response, array $args) : Response {
        $this->logPath($request, __METHOD__);
        $responseBody = [
            'result'=>true,
            'id' => $args['id']
        ];
        return $this->respondWithData($response, $responseBody);
    }

    public function userLogin(Request $request, Response $response, array $args) : Response {
        $this->logPath($request, __METHOD__);
        $requestBody = new UserLoginRequest(json_decode($request->getBody()));

        $userLoginResult = $this->userService->userLogin($requestBody);


        return $this->respondWithData($response, $userLoginResult, 200);
    }
}