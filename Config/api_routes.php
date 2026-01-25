<?php

declare(strict_types=1);

require_once __DIR__ . '/../Controllers/UsersController.php';
require_once __DIR__ . '/../Controllers/ArticlesController.php';
require_once __DIR__ . '/../Controllers/CategoriesController.php';
require_once __DIR__ . '/../Controllers/ArticleController.php';
require_once __DIR__ . '/../Controllers/CategoryController.php';
require_once __DIR__ . '/../Controllers/CommentsController.php';
require_once __DIR__ . '/../Controllers/LikesController.php';
require_once __DIR__ . '/../Controllers/PdfEditionsController.php';
require_once __DIR__ . '/../Controllers/OrdersController.php';
require_once __DIR__ . '/../Controllers/DownloadsController.php';
require_once __DIR__ . '/../Controllers/PagesController.php';
require_once __DIR__ . '/../Controllers/MediaController.php';
require_once __DIR__ . '/../Controllers/AuthController.php';

const API_RESOURCE_CONTROLLERS = [
    'users' => UsersController::class,
    'articles' => ArticleController::class,
    'categories' => CategoryController::class,
    'article' => ArticleController::class,
    'category' => CategoryController::class,
    'comments' => CommentsController::class,
    'likes' => LikesController::class,
    'pdf-editions' => PdfEditionsController::class,
    'pdf_editions' => PdfEditionsController::class,
    'orders' => OrdersController::class,
    'downloads' => DownloadsController::class,
    'pages' => PagesController::class,
    'media' => MediaController::class,
];

function dispatch_api_request(string $uri, string $method): void
{
    $path = trim(substr($uri, 4), '/');
    $segments = $path === '' ? [] : explode('/', $path);
    $resource = $segments[0] ?? '';
    $id = $segments[1] ?? null;

    if ($resource === 'auth') {
        $action = $segments[1] ?? '';
        $authController = new AuthController();

        if ($method !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Méthode non autorisée.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return;
        }

        if ($action === 'register') {
            $authController->register();
            return;
        }

        if ($action === 'login') {
            $authController->login();
            return;
        }

        if ($action === 'logout') {
            $authController->logout();
            return;
        }

        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Action auth introuvable.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    if ($resource === '' || ! isset(API_RESOURCE_CONTROLLERS[$resource])) {
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Ressource API introuvable.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    if ($id !== null && ! ctype_digit($id)) {
        http_response_code(400);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Identifiant invalide.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $controllerClass = API_RESOURCE_CONTROLLERS[$resource];
    $controller = new $controllerClass();

    switch ($method) {
        case 'GET':
            if ($id === null) {
                $controller->index();
                return;
            }
            $controller->show((int) $id);
            return;
        case 'POST':
            if ($id !== null) {
                http_response_code(400);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'La création ne prend pas d\'identifiant.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                return;
            }
            $controller->store();
            return;
        case 'PUT':
        case 'PATCH':
            if ($id === null) {
                http_response_code(400);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'Identifiant requis pour la mise à jour.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                return;
            }
            $controller->update((int) $id);
            return;
        case 'DELETE':
            if ($id === null) {
                http_response_code(400);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'Identifiant requis pour la suppression.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                return;
            }
            $controller->destroy((int) $id);
            return;
        default:
            http_response_code(405);
            header('Allow: GET, POST, PUT, PATCH, DELETE');
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Méthode non autorisée.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
