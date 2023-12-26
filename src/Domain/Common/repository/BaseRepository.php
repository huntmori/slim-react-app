<?php

namespace App\Domain\Common\repository;
use App\Application\Common\ObjectPool;
use PDO;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class BaseRepository
{
    protected PDO $pdo;
    protected ObjectPool $connectionPool;
    protected LoggerInterface $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->pdo = $container->get(PDO::class);
        $this->logger = $container->get(LoggerInterface::class);
//        $this->connectionPool = $container->get(ObjectPool::class);
    }

    public function getPdo() : PDO
    {
//        return $this->connectionPool->get($this->connectionPool->createParams);
        return $this->pdo;
    }

    public function disposePdo(PDO $pdo): void
    {
//        $this->connectionPool->dispose($pdo);
    }
}