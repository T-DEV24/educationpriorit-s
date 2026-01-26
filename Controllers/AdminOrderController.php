<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/OrderModel.php';
require_once __DIR__ . '/../Config/auth.php';

class AdminOrderController extends BaseController
{
    public function __construct()
    {
        $this->model = new OrderModel();
    }

    public function index(): void
    {
        if (! AuthSession::isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 20;
        $data = $this->model->findAdminPaginated($page, $limit);

        $this->json(['data' => $data['items'], 'pagination' => $data['pagination']]);
    }
}
