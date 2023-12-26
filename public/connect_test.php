<?php

$host = "127.0.0.1";
$port = 1935;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_connect($socket, $host, $port);

$message = '{"id": "kknd5050","password": "1q2w3e4r!","client_type": "web","client_id": "","nothing":"empty"}';

socket_write($socket, $message, strlen($message));

$response = socket_read($socket, 1024);

echo "Response : " .$response.PHP_EOL;

socket_close($socket);
