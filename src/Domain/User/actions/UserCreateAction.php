<?php

namespace App\Domain\User\actions;

use App\Domain\User\actions\base\UserAction;
use App\Domain\User\models\UserCreateRequest;
use Psr\Http\Message\ResponseInterface as Response;

class UserCreateAction extends UserAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $requestBody = $this->request->getBody();
        $this->logger->info("request-body: ". $requestBody);
        $jsonDecode = json_decode($requestBody, true);

        $request = new UserCreateRequest($jsonDecode);
        $user = $this->userService->userCreate($request);

        return $this->respondWithData([
            'idx'=>$user->get('idx')
        ]);
    }
}