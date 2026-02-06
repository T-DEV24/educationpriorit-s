<?php

declare(strict_types=1);

$baseDir = dirname(__DIR__);
$appEnv = strtolower((string) (getenv('APP_ENV') ?: 'production'));
$serverName = $_SERVER['SERVER_NAME'] ?? '';
$isLocal = in_array($appEnv, ['local', 'development', 'dev'], true)
    || in_array($serverName, ['localhost', '127.0.0.1'], true);

ini_set('log_errors', '1');
$logDir = $baseDir . '/storage/logs';
if (! is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}
ini_set('error_log', $logDir . '/php-error.log');

if ($isLocal) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

require $baseDir . '/Config/routes.php';
require $baseDir . '/Config/api_routes.php';
require $baseDir . '/Config/auth.php';

AuthSession::start();

function render_view(string $view, array $data = [], string $title = ''): void
{
    extract($data, EXTR_SKIP);

    ob_start();
    require __DIR__ . '/../Views/' . $view . '.php';
    $content = ob_get_clean();

    require __DIR__ . '/../Views/layouts/main.php';
}

$rawUrl = $_GET['url'] ?? '';
$rawUrl = is_string($rawUrl) ? $rawUrl : '';
if ($rawUrl === '') {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $requestPath = parse_url((string) $requestUri, PHP_URL_PATH) ?? '';
    $requestPath = is_string($requestPath) ? $requestPath : '';
    if ($requestPath !== '' && $requestPath !== '/index.php') {
        $rawUrl = ltrim($requestPath, '/');
    }
}
$sanitizedUrl = filter_var($rawUrl, FILTER_SANITIZE_URL) ?: '';
$sanitizedUrl = trim($sanitizedUrl, '/');
$segments = $sanitizedUrl === '' ? [] : array_values(array_filter(explode('/', $sanitizedUrl), 'strlen'));
$routePath = '/' . implode('/', $segments);
if ($routePath === '/') {
    $routePath = '/';
}

if ($routePath === '/api' || strncmp($routePath, '/api/', 5) === 0) {
    dispatch_api_request($routePath, $_SERVER['REQUEST_METHOD'] ?? 'GET');
    exit;
}

$routes = ROUTE_LIST;
$route = $routes[$routePath] ?? null;

if ($route === null) {
    if (preg_match('#^/article/([^/]+)$#', $routePath, $matches)) {
        render_view('front/article', ['slug' => $matches[1]], 'Article');
        exit;
    }

    if (preg_match('#^/rubriques/([^/]+)$#', $routePath, $matches)) {
        render_view('front/rubrique', ['slug' => $matches[1], 'rubrique' => ucfirst($matches[1])], 'Rubrique');
        exit;
    }

    http_response_code(404);
    render_view('404', [], 'Page introuvable');
    exit;
}

$data = $route['data'] ?? [];
$title = $route['title'] ?? '';
render_view($route['view'], $data, $title);
