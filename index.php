<?php

require __DIR__ . '/Config/routes.php';
require __DIR__ . '/Config/api_routes.php';

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
    http_response_code(404);
    render_view('404', [], 'Page introuvable');
    exit;
}

$data = $route['data'] ?? [];
$title = $route['title'] ?? '';
render_view($route['view'], $data, $title);
