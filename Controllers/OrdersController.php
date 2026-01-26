<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/OrderModel.php';

class OrdersController extends BaseController
{
    public function __construct()
    {
        $this->model = new OrderModel();
    }
}
