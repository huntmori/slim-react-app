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

    public function selectOne(string $sql, array $paramMap, string $returnType) {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare($sql);

        $keys = array_keys($paramMap);
        for ($i=0; $i<count($keys); $i++) {
            $key = $keys[$i];
            $stmt->bindValue($key, $paramMap[$key]);
        }

        $stmt->execute();
        $result = $stmt->fetchObject($returnType);
        $this->disposePdo($pdo);
        return $result;
    }
}