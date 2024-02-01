<?php

namespace App\Domain\User\service;

use App\Domain\Profile\entities\Profile;
use App\Domain\User\entities\User;
use App\Domain\User\models\UserCreateRequest;
use App\Domain\User\models\UserLoginRequest;
use App\Domain\User\models\UserLoginResponse;

interface UserService
{

    public function userCreate(UserCreateRequest $request) : User;
    public function userCreateParams(
        string $id,
        string $userName,
        string $email,
        string $password
    ) : User;

    public function userLogin(UserLoginRequest $requestBody) : UserLoginResponse;
    public function getUserJwtToken(User $user, Profile $primaryProfile) : string;

    public function getUserByUserId(string $userId) : ?User;
    public function getUserByUserIdx(int $userIdx) : ?User;

}