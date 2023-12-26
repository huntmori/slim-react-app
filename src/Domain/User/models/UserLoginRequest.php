<?php

namespace App\Domain\User\models;

use App\Domain\Common\models\BaseDto;

class UserLoginRequest extends BaseDto
{
    public string $id;
    public string $password;
    public string $client_type;
    public string $client_id;

    function __construct($params = null)
    {
        parent::__construct($params);
    }
}