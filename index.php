<?php

require __DIR__ . '/Config/routes.php';

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
