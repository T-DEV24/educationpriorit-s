<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/CategoryModel.php';
require_once __DIR__ . '/../Config/auth.php';

class CategoryController extends BaseController
{
    public function __construct()
    {
        $this->model = new CategoryModel();
    }

    public function index(): void
    {
        $slug = trim((string) filter_input(INPUT_GET, 'slug', FILTER_UNSAFE_RAW));
        if ($slug !== '') {
            $category = $this->model->findBySlug($slug);
            if ($category === null) {
                $this->json(['error' => 'Catégorie introuvable.'], 404);
                return;
            }
            $this->json(['data' => $category]);
            return;
        }

        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 20;
        $withCounts = filter_input(INPUT_GET, 'with_counts', FILTER_VALIDATE_BOOLEAN);

        if ($withCounts) {
            $data = $this->model->findPaginatedWithCounts($page, $limit);
        } else {
            $data = $this->model->findPaginated($page, $limit);
        }
        $this->json(['data' => $data['items'], 'pagination' => $data['pagination']]);
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
