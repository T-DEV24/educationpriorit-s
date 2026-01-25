<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/CommentModel.php';

class CommentController extends BaseController
{
    public function __construct()
    {
        $this->model = new CommentModel();
    }

    public function store(): void
    {
        $userId = $this->requireUserId();
        if ($userId === null) {
            return;
        }

        $payload = $this->getRequestData();
        $articleId = (int) ($payload['article_id'] ?? 0);
        $content = trim((string) ($payload['content'] ?? ''));

        if ($articleId <= 0 || $content === '') {
            $this->json(['error' => 'Article et contenu requis.'], 422);
            return;
        }

        $data = [
            'article_id' => $articleId,
            'user_id' => $userId,
            'content' => $content,
            'is_approved' => (int) ($payload['is_approved'] ?? 0),
        ];

        $created = $this->model->create($data);
        if ($created === null) {
            $this->json(['error' => 'Impossible de créer le commentaire.'], 500);
            return;
        }

        $this->json(['data' => $created], 201);
    }

    private function requireUserId(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->json(['error' => 'Connexion requise.'], 401);
            return null;
        }

        return $userId;
    }
}
