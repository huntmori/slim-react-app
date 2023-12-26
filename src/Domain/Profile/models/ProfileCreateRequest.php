<?php

namespace App\Domain\Profile\models;

use App\Domain\Common\models\BaseDto;

class ProfileCreateRequest extends BaseDto
{
    private ?string $nickname;

    public function __construct($params = null)
    {
        parent::__construct($params);
    }
}