<?php

require __DIR__ . '/Config/routes.php';
require __DIR__ . '/Config/api_routes.php';
require __DIR__ . '/Config/auth.php';

AuthSession::start();

function render_view(string $view, array $data = [], string $title = ''): void
{
    extract($data, EXTR_SKIP);

    ob_start();
    require __DIR__ . '/Views/' . $view . '.php';
    $content = ob_get_clean();

    require __DIR__ . '/Views/layouts/main.php';
}

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$uri = rtrim($uri, '/') ?: '/';

if (strncmp($uri, '/api', 4) === 0) {
    dispatch_api_request($uri, $_SERVER['REQUEST_METHOD'] ?? 'GET');
    exit;
}

$routes = ROUTE_LIST;
$route = $routes[$uri] ?? null;

if ($route === null) {
    if (preg_match('#^/article/([^/]+)$#', $uri, $matches)) {
        render_view('front/article', ['slug' => $matches[1]], 'Article');
        exit;
    }

    if (preg_match('#^/rubriques/([^/]+)$#', $uri, $matches)) {
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
