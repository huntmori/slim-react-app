<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$port = 1935;
$loop = \React\EventLoop\Loop::get();
$socket = new React\Socket\SocketServer("127.0.0.1:{$port}", [], $loop);

$socket->on('connection', function(\React\Socket\ConnectionInterface $connection) {

    $connection->on('data',function($data) use($connection) {

        echo $data.PHP_EOL;
    });
});

$loop->run();