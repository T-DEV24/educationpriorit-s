<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/LikeModel.php';
require_once __DIR__ . '/../Config/auth.php';

class LikeController extends BaseController
{
    public function __construct()
    {
        $this->model = new LikeModel();
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

        if ($articleId <= 0) {
            $this->json(['error' => 'Article requis.'], 422);
            return;
        }

        if ($this->model->findByUserAndArticle($userId, $articleId) !== null) {
            $this->json(['error' => 'Like déjà enregistré.'], 409);
            return;
        }

        $created = $this->model->create([
            'article_id' => $articleId,
            'user_id' => $userId,
        ]);

        if ($created === null) {
            $this->json(['error' => 'Impossible de créer le like.'], 500);
            return;
        }

        $this->json(['data' => $created], 201);
    }

}
