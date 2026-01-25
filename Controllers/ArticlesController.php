<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Article.php';

class ArticlesController extends BaseController
{
    public function __construct()
    {
        $this->model = new Article();
    }
}
