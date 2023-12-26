<?php

namespace App\Domain\User\models;

use App\Domain\Common\models\BaseDto;

class UserCreateRequest extends BaseDto
{
    public string $id;
    public string $userName;
    public string $email;
    public string $password;

    public function __construct($params = null)
    {
        parent::__construct($params);
    }
}