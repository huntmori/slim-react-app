<?php

declare(strict_types=1);

use App\Domain\Profile\Repository\ProfileRepository;
use App\Domain\Profile\Repository\ProfileRepositoryImplement;
use App\Domain\Profile\service\ProfileService;
use App\Domain\Profile\service\ProfileServiceImplement;
use App\Domain\User\repository\UserRepository;
use App\Domain\User\repository\UserRepositoryImplement;
use App\Domain\User\service\UserService;
use App\Domain\User\service\UserServiceImplement;
use DI\ContainerBuilder;
use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
//    $containerBuilder->addDefinitions([
//        UserRepository::class => autowire(InMemoryUserRepository::class),
//    ]);

    $containerBuilder->addDefinitions([
        UserService::class => autowire(UserServiceImplement::class),
    ]);
    $containerBuilder->addDefinitions([
        UserRepository::class => autowire(UserRepositoryImplement::class),
    ]);
    $containerBuilder->addDefinitions([
        ProfileService::class => autowire(ProfileServiceImplement::class),
    ]);
    $containerBuilder->addDefinitions([
       ProfileRepository::class => autowire(ProfileRepositoryImplement::class)
    ]);
};
