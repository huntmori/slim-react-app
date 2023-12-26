<?php

declare(strict_types=1);

namespace App\Domain\User\actions;

use App\Domain\User\actions\base\UserAction;
use Psr\Http\Message\ResponseInterface as Response;

class ViewUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');
        $user = [];

        return $this->respondWithData($user);
    }
}
