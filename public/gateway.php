<?php

declare(strict_types=1);

use App\Application\Common\ObjectPool;
use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Http\Browser;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

$backends = [];
$size = 30;
$startPort = 8081;
for($i=0;$i<$size;$i++) {
    $serverInfo = new stdClass();
    $serverInfo->port = $startPort+$i;
    $serverInfo->host = '127.0.0.1';
    $serverInfo->scheme = 'http';
    $backends[] = $serverInfo;
}

$loop = Loop::get();
$browser = new Browser($loop);

$currentIndex = 0;

$gateway = new HttpServer(function(ServerRequestInterface $request) use ($browser, $backends, &$currentIndex) {
    $serverInfo  = $backends[$currentIndex];
    $currentIndex = ($currentIndex + 1) % count($backends);

    $requestUrl = "{$serverInfo->scheme}://{$serverInfo->host}:{$serverInfo->port}{$request->getUri()->getPath()}";
    //echo $requestUrl.PHP_EOL;
    return $browser->request(
        $request->getMethod(),
        $requestUrl,
        $request->getHeaders(),
        $request->getBody()
    )->then(
        function (Psr\Http\Message\ResponseInterface $response) use ($requestUrl) {
            echo $requestUrl."==>".$response->getStatusCode().PHP_EOL;
            return $response;
        },
        function(Exception $e) use ($requestUrl) {
            echo $e->getMessage().PHP_EOL;
            echo $requestUrl."==>500".PHP_EOL;
            return StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
        }
    );
});

$gatewaySocket = new SocketServer("127.0.0.1:3000", [], $loop);
$gateway->listen($gatewaySocket);

$loop->run();