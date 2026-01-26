<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/OrderModel.php';
require_once __DIR__ . '/../Config/auth.php';

class OrdersController extends BaseController
{
    public function __construct()
    {
        $this->model = new OrderModel();
    }

    public function index(): void
    {
        $mine = filter_input(INPUT_GET, 'mine', FILTER_VALIDATE_BOOLEAN);
        if ($mine) {
            $userId = AuthSession::requireUserId(function (): void {
                $this->json(['error' => 'Connexion requise.'], 401);
            });
            if ($userId === null) {
                return;
            }

            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
            $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 10;

            $data = $this->model->findByUserPaginated($userId, $page, $limit);
            $this->json(['data' => $data['items'], 'pagination' => $data['pagination']]);
            return;
        }

        parent::index();
    }
}
