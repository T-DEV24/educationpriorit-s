<?php

require __DIR__ . '/Config/bootstrap.php';

$router = require __DIR__ . '/Config/routes.php';

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$uri = rtrim($uri, '/') ?: '/';
$method = request_method();

$router->dispatch($method, $uri);
