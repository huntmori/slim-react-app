<?php

namespace App\Application\Common;

use React\EventLoop\Loop;
use React\EventLoop\StreamSelectLoop;
use React\Http\Browser;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Slim\App;

class ServerInfo
{
    public ?string $host;
    public ?int $port;
    public ?string $scheme;
    public ?HttpServer $httpServer;
    public ?SocketServer $socketServer;
    public ?App $serverApp;
    public ?Browser $browser;

}