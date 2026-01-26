<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/CommentModel.php';
require_once __DIR__ . '/../Config/auth.php';

class CommentController extends BaseController
{
    public function __construct()
    {
        $this->model = new CommentModel();
    }

    public function index(): void
    {
        $articleId = filter_input(INPUT_GET, 'article_id', FILTER_VALIDATE_INT) ?: 0;
        if ($articleId > 0) {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
            $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 10;
            $includePending = filter_input(INPUT_GET, 'include_pending', FILTER_VALIDATE_BOOLEAN);

            $data = $this->model->findByArticlePaginated($articleId, $page, $limit, ! $includePending);
            $this->json(['data' => $data['items'], 'pagination' => $data['pagination']]);
            return;
        }

        parent::index();
    }

    public function store(): void
    {
        $userId = AuthSession::requireUserId(function (): void {
            $this->json(['error' => 'Connexion requise.'], 401);
        });
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

}
