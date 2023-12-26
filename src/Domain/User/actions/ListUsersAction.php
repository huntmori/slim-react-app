<?php

declare(strict_types=1);

namespace App\Domain\User\actions;

use App\Domain\User\actions\base\UserAction;
use Psr\Http\Message\ResponseInterface as Response;

class ListUsersAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $users = [];

        return $this->respondWithData($users);
    }
}
