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

    public function selectList(string $sql, array $paramMap, string $returnType) : array {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare($sql);

        $keys = array_keys($paramMap);
        for ($i=0; $i<count($keys); $i++) {
            $key = $keys[$i];
            $stmt->bindValue($key, $paramMap[$key]);
        }

        $stmt->execute();
        $array = [];
        while($row = $stmt->fetchObject($returnType)) {
            $array[] = $row;
        }
        $this->disposePdo($pdo);
        return $array;
    }

    public function update(string $sql, array $paramMap): bool
    {
        $pdo = $this->getPdo();
        echo $sql.PHP_EOL;
        $stmt = $pdo->prepare($sql);

        $keys = array_keys($paramMap);
        for ($i=0; $i<count($keys); $i++) {
            $key = $keys[$i];
            $stmt->bindValue($key, $paramMap[$key]);
        }

        $result = $stmt->execute();
        $this->disposePdo($pdo);
        return $result;
    }
}