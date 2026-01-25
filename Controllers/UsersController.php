<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/User.php';

class UsersController extends BaseController
{
    public function __construct()
    {
        $this->model = new User();
    }
}
