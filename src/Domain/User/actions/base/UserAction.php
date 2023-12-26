<?php

declare(strict_types=1);

namespace App\Domain\User\actions\base;

use App\Application\Actions\Action;
use App\Domain\User\service\UserService;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    protected UserService $userService;

    public function __construct(
        LoggerInterface $logger,
        UserService $userService
    ) {
        parent::__construct($logger);
        $this->userService = $userService;
    }
}
