<?php

declare(strict_types=1);

use App\Application\Common\ObjectPool;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        PDO::class => function(ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $config = $settings->get('config');
            echo json_encode($config).PHP_EOL;
            return new PDO(
              "mysql:host=".$config['DB_HOST'].
                    ";dbname=".$config['DB_NAME'],
                $config['DB_USER'],
                $config['DB_PASSWORD']
            );
        }
    ]);

    $containerBuilder->addDefinitions([
        ObjectPool::class => function(ContainerInterface $c) {
            $objectPool = new ObjectPool();
            $objectPool->logDisable = true;
            $objectPool->poolName = uniqid();
            $objectPool->createParams = $c->get('config');
            $objectPool->createNewDelegate = function(array $config) {
                echo '+++++++++++++++++++++++++++++++++++object pool create delegate called '.PHP_EOL;

                return new PDO(
                    "mysql:host=".$config['DB_HOST'].
                    ";dbname=".$config['DB_NAME'],
                    $config['DB_USER'],
                    $config['DB_PASSWORD']
                );
            };
            $array = [];
            for($i=0; $i<10; $i++) {
                $array[] = $objectPool->get($objectPool->createParams);
            }
            for($i=0; $i<10; $i++) {
                $objectPool->dispose($array[$i]);
            }
            return $objectPool;
        }
    ]);

    $containerBuilder->addDefinitions([
        Predis\Client::class => function(ContainerInterface $c) {
            /** @var SettingsInterface $settings */
            $settings = $c->get(SettingsInterface::class);

            /** @var array $config */
            $config = $settings->get('config');
            return new Predis\Client([
                'scheme' => $config['REDIS_SCHEME'],
                'host' => $config['REDIS_HOST'],
                'port' => $config['REDIS_PORT']
            ]);
        }
    ]);
};