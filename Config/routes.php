<?php

$router = new Router();

$router->get('/', ['FrontController', 'home']);
$router->get('/rubriques', ['FrontController', 'rubriques']);
$router->get('/rubrique/{slug}', ['FrontController', 'rubrique']);
$router->get('/article/{slug}', ['FrontController', 'article']);
$router->post('/article/{slug}/comment', ['FrontController', 'comment']);
$router->post('/article/{slug}/like', ['FrontController', 'like']);
$router->get('/boutique', ['FrontController', 'boutique']);
$router->get('/boutique/{slug}', ['FrontController', 'boutiqueDetail']);
$router->post('/boutique/{slug}/acheter', ['FrontController', 'purchase']);
$router->get('/telechargement/{id}', ['FrontController', 'download']);
$router->get('/a-propos', ['FrontController', 'about']);
$router->get('/contact', ['FrontController', 'contact']);
$router->get('/recherche', ['FrontController', 'search']);

$router->get('/connexion', ['AuthController', 'login']);
$router->post('/connexion', ['AuthController', 'login']);
$router->get('/inscription', ['AuthController', 'register']);
$router->post('/inscription', ['AuthController', 'register']);
$router->get('/deconnexion', ['AuthController', 'logout']);

$router->get('/profil', ['UserController', 'profile']);
$router->get('/profil/commentaires', ['UserController', 'comments']);
$router->get('/profil/achats', ['UserController', 'purchases']);
$router->get('/mot-de-passe', ['UserController', 'password']);
$router->post('/mot-de-passe', ['UserController', 'password']);

$router->get('/admin', ['AdminController', 'dashboard']);
$router->get('/admin/articles', ['AdminController', 'articles']);
$router->post('/admin/articles', ['AdminController', 'articles']);
$router->post('/admin/articles/{id}/status', ['AdminController', 'articleStatus']);
$router->get('/admin/categories', ['AdminController', 'categories']);
$router->post('/admin/categories', ['AdminController', 'categories']);
$router->get('/admin/comments', ['AdminController', 'comments']);
$router->post('/admin/comments', ['AdminController', 'comments']);
$router->get('/admin/pdf', ['AdminController', 'pdf']);
$router->post('/admin/pdf', ['AdminController', 'pdf']);
$router->get('/admin/pages', ['AdminController', 'pages']);
$router->post('/admin/pages', ['AdminController', 'pages']);
$router->get('/admin/media', ['AdminController', 'media']);
$router->post('/admin/media', ['AdminController', 'media']);
$router->get('/admin/users', ['AdminController', 'users']);
$router->post('/admin/users', ['AdminController', 'users']);
$router->get('/admin/orders', ['AdminController', 'orders']);

return $router;
