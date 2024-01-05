<?php

declare(strict_types=1);

use App\Application\Common\ObjectPool;
use App\Application\Common\ServerInfo;
use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Http\Browser;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

$build = function (): App {
    // Instantiate PHP-DI ContainerBuilder
        $containerBuilder = new ContainerBuilder();

        if (false) { // Should be set to true in production
            $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
        }

    // Set up settings
        $settings = require __DIR__ . '/../app/settings.php';
        $settings($containerBuilder);

    // Set up dependencies
        $dependencies = require __DIR__ . '/../app/dependencies.php';
        $dependencies($containerBuilder);

    // Set up repositories
        $repositories = require __DIR__ . '/../app/repositories.php';
        $repositories($containerBuilder);

        $dbConfig = require __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "app". DIRECTORY_SEPARATOR."databaseConfig.php";
        $dbConfig($containerBuilder);

    // Build PHP-DI Container instance
        $container = $containerBuilder->build();

    // Instantiate the app
        AppFactory::setContainer($container);
        $app = AppFactory::create();
        $callableResolver = $app->getCallableResolver();

    // Register middleware
        $middleware = require __DIR__ . '/../app/middleware.php';
        $middleware($app);

    // Register routes
        $routes = require __DIR__ . '/../app/routes.php';
        $routes($app);

        /** @var SettingsInterface $settings */
        $settings = $container->get(SettingsInterface::class);

        $displayErrorDetails = $settings->get('displayErrorDetails');
        $logError = $settings->get('logError');
        $logErrorDetails = $settings->get('logErrorDetails');

    // Create Request object from globals
        $serverRequestCreator = ServerRequestCreatorFactory::create();
        $request = $serverRequestCreator->createServerRequestFromGlobals();

    // Create Error Handler
        $responseFactory = $app->getResponseFactory();
        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

    // Create Shutdown Handler
        $shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
        register_shutdown_function($shutdownHandler);

    // Add Routing Middleware
        $app->addRoutingMiddleware();

    // Add Body Parsing Middleware
        $app->addBodyParsingMiddleware();

    // Add Error Middleware
        $errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

    // Run App & Emit Response
        $response = $app->handle($request);
        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);
        return $app;
};

$apps = [];
$currentApp = $build();
$port = 8081;

$portCheckFunction = function($host, $port) {
    return false;
//
//    echo "try connection {$host}:{$port}".PHP_EOL;
//    $connection = @fsockopen($host, $port);
//
//    if(is_resource($connection)) {
//        fclose($connection);
//        return true;
//    }
//
//    return false;
//
};

$serverBuild = function($app, $port) : ServerInfo
{
    echo $port.PHP_EOL;
    $http = new HttpServer(
        new React\Http\Middleware\StreamingRequestMiddleware(),
        new React\Http\Middleware\LimitConcurrentRequestsMiddleware(1000), // 100 concurrent buffering handlers
        new React\Http\Middleware\RequestBodyBufferMiddleware(2 * 1024 * 1024*1000), // 2 MiB per request
        new React\Http\Middleware\RequestBodyParserMiddleware(),
        function(ServerRequestInterface $request) use ($app, $port) {
        $now = date("Y-m-d H:i:s:u");
        echo "[{$now}]:{$port}=>request in coming {$port}".PHP_EOL;
        echo "[{$now}]:{$port}=>".$request->getUri().PHP_EOL;

        return $app->handle($request);;
    });

    $socket = new SocketServer('127.0.0.1:'.$port);
    $http->listen($socket);
    echo 'server run on 127.0.0.1:'.$port.PHP_EOL;

    $serverInfo = new ServerInfo();
    $serverInfo->port = $port;
    $serverInfo->host = '127.0.0.1';
    $serverInfo->scheme = 'http';
    $serverInfo->httpServer = $http;
    $serverInfo->socketServer = $socket;
    $serverInfo->serverApp = $app;
    return $serverInfo;
};

$startPort = 8080;
$index = 0;

$size = 1;
$servers = [];
$serverInfos = [];
$maxPort = $startPort;
$loop = Loop::get();
for($i=0; count($serverInfos)<$size; $i++) {
    $maxPort = $maxPort + $i;
    echo $maxPort;
    $app = $build();
    if(!$portCheckFunction('127.0.0.1', $maxPort)) {
        $serverInfo = $serverBuild($app, $maxPort);

        $serverInfos[] = $serverInfo;

        echo $maxPort. " 8i open ".PHP_EOL;
    } else {
        echo $maxPort. "is already used. it will be skipped".PHP_EOL;
    }
}
//
//$currentIndex = 0;
//$serverPool = new ObjectPool();
//$serverPool->poolName = 'backend-server-pool';
//$serverPool->setAvailable($serverInfos);
//$serverPool->createNewDelegate = $serverBuild;
//
//
//$gateway = new HttpServer(
//    new React\Http\Middleware\StreamingRequestMiddleware(),
//    new React\Http\Middleware\LimitConcurrentRequestsMiddleware(100000), // 100 concurrent buffering handlers
//    new React\Http\Middleware\RequestBodyBufferMiddleware(2 * 1024 * 1024 * 100000), // 2 MiB per request
//    new React\Http\Middleware\RequestBodyParserMiddleware(),
//    function(ServerRequestInterface $request) use ($serverPool, $serverInfos, $currentApp, &$maxPort, $portCheckFunction, $loop) {
//
//        $serverInfo  = $serverInfos[0];
//
//        echo $maxPort.PHP_EOL;
//        $requestUrl = "{$serverInfo->scheme}://{$serverInfo->host}:{$serverInfo->port}{$request->getUri()->getPath()}";
//
//        $browser = new Browser($loop);
//        return $browser->request(
//            $request->getMethod(),
//            $requestUrl,
//            $request->getHeaders(),
//            $request->getBody()
//        )->then(
//            function (Psr\Http\Message\ResponseInterface $response) use ($requestUrl, $browser, $serverInfo, $serverPool) {
//                echo $requestUrl."==>".$response->getStatusCode().PHP_EOL;
////                $serverPool->dispose($serverInfo);
//                return $response;
//            },
//            function(Exception $e) use ($requestUrl, $serverPool, $serverInfo, $browser) {
//                echo $e->getMessage().PHP_EOL;
//                echo $requestUrl."==>500".PHP_EOL;
////                $serverPool->dispose($serverInfo);
//                return StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
//            }
//        );
//});
//
//$gatewaySocket = new SocketServer("127.0.0.1:3000");
//$gateway->listen($gatewaySocket);
//echo 'gateway listen 127.0.0.1:3000'.PHP_EOL;
//echo 'backend pool size is '.PHP_EOL;
//$serverPool->printSizeViaLogger("");

$loop->run();