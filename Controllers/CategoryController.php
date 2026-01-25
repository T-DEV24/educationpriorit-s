<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/CategoryModel.php';

class CategoryController extends BaseController
{
    public function __construct()
    {
        $this->model = new CategoryModel();
    }

    public function index(): void
    {
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 20;

        $data = $this->model->findPaginated($page, $limit);
        $this->json(['data' => $data['items'], 'pagination' => $data['pagination']]);
    }

    public function store(): void
    {
        if (! $this->isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        parent::store();
    }

    public function update(int $id): void
    {
        if (! $this->isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        parent::update($id);
    }

    public function destroy(int $id): void
    {
        if (! $this->isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        parent::destroy($id);
    }

    private function isAdmin(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int) ($_SESSION['role_id'] ?? 0) === 1;
    }
}
