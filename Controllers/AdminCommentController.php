<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/CommentModel.php';

class AdminCommentController extends BaseController
{
    public function __construct()
    {
        $this->model = new CommentModel();
    }

    public function index(): void
    {
        if (! $this->isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        parent::index();
    }

    public function show(int $id): void
    {
        if (! $this->isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        parent::show($id);
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
