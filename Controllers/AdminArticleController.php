<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/ArticleModel.php';
require_once __DIR__ . '/../Config/auth.php';

class AdminArticleController extends BaseController
{
    public function __construct()
    {
        $this->model = new ArticleModel();
    }

    public function index(): void
    {
        if (! AuthSession::isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 20;

        $data = $this->model->findPaginated($page, $limit);
        $this->json(['data' => $data['items'], 'pagination' => $data['pagination']]);
    }

    public function show(int $id): void
    {
        if (! AuthSession::isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        $article = $this->model->findWithTags($id);
        if ($article === null) {
            $this->json(['error' => 'Ressource introuvable.'], 404);
            return;
        }

        $this->json(['data' => $article]);
    }

    public function store(): void
    {
        if (! AuthSession::isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        $payload = $this->getRequestData();
        $tagIds = $payload['tag_ids'] ?? [];
        unset($payload['tag_ids']);

        $created = $this->model->createWithTags($payload, is_array($tagIds) ? $tagIds : []);
        if ($created === null) {
            $this->json(['error' => 'Impossible de créer l’article.'], 500);
            return;
        }

        $this->json(['data' => $created], 201);
    }

    public function update(int $id): void
    {
        if (! AuthSession::isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        $payload = $this->getRequestData();
        $tagIds = $payload['tag_ids'] ?? [];
        unset($payload['tag_ids']);

        $updated = $this->model->updateWithTags($id, $payload, is_array($tagIds) ? $tagIds : []);
        if ($updated === null) {
            $this->json(['error' => 'Impossible de mettre à jour l’article.'], 500);
            return;
        }

        $this->json(['data' => $updated]);
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
