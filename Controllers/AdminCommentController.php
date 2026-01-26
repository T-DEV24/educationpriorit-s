<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/CommentModel.php';
require_once __DIR__ . '/../Config/auth.php';

class AdminCommentController extends BaseController
{
    public function __construct()
    {
        $this->model = new CommentModel();
    }

    public function index(): void
    {
        if (! AuthSession::isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 20;
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));
        $offset = ($page - 1) * $limit;

        $total = $this->model->countAll();
        $items = $this->model->findAll($limit, $offset);

        $this->json([
            'data' => $items,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => (int) ceil($total / $limit),
            ],
        ]);
    }

    public function show(int $id): void
    {
        if (! AuthSession::isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        parent::show($id);
    }

    public function store(): void
    {
        if (! AuthSession::isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        parent::store();
    }

    public function update(int $id): void
    {
        if (! AuthSession::isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        parent::update($id);
    }

    public function destroy(int $id): void
    {
        if (! AuthSession::isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        parent::destroy($id);
    }
}
