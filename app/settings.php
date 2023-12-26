<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            $ROOT_PATH = __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR;
//            echo $ROOT_PATH.PHP_EOL;
//            $profileEnv = Dotenv::createImmutable(
//                $ROOT_PATH,
//                ".env.profile"
//            );
//            $profileLoaded = $profileEnv->load();
//            var_dump($profileLoaded);
//            //$stringProfile = $profileLoaded['PROFILE'];
//            $stringProfile = 'local';
//
//            $configEnv = Dotenv::createImmutable(
//                $ROOT_PATH,
//                ".env.{$stringProfile}"
//            );
//            $config = $configEnv->load();
            $stringProfile = "local";
            $string = file_get_contents($ROOT_PATH.".env.local.json");
            $config = json_decode($string, true);
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'profile'=>$stringProfile,
                'config'=>$config,
                'dbUri'=>rawurldecode($config['DB_USER']).":".rawurlencode($config['DB_PASSWORD'])."@".$config['DB_HOST'].":".$config['DB_PORT']."/".$config['DB_NAME']
            ]);
        }
    ]);
};
