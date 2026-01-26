<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/ArticleModel.php';
require_once __DIR__ . '/../Models/CategoryModel.php';
require_once __DIR__ . '/../Config/auth.php';

class ArticleController extends BaseController
{
    public function __construct()
    {
        $this->model = new ArticleModel();
    }

    public function index(): void
    {
        $slug = trim((string) filter_input(INPUT_GET, 'slug', FILTER_UNSAFE_RAW));
        $statusParam = trim((string) filter_input(INPUT_GET, 'status', FILTER_UNSAFE_RAW));
        $status = null;
        if (in_array($statusParam, ['draft', 'published'], true)) {
            $status = $statusParam;
        } elseif ($statusParam !== 'all') {
            $status = 'published';
        }

        if ($slug !== '') {
            $article = $this->model->findBySlug($slug, $status);
            if ($article === null) {
                $this->json(['error' => 'Article introuvable.'], 404);
                return;
            }
            $this->json(['data' => $article]);
            return;
        }

        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 12;
        $search = trim((string) filter_input(INPUT_GET, 'search', FILTER_UNSAFE_RAW));
        $categoryId = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
        $categoryId = $categoryId !== false && $categoryId !== null ? $categoryId : null;
        $categorySlug = trim((string) filter_input(INPUT_GET, 'category', FILTER_UNSAFE_RAW));

        if ($categoryId === null && $categorySlug !== '') {
            $categoryModel = new CategoryModel();
            $category = $categoryModel->findBySlug($categorySlug);
            $categoryId = $category['id'] ?? null;
            if ($categoryId === null) {
                $this->json([
                    'data' => [],
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => 0,
                        'pages' => 0,
                    ],
                ]);
                return;
            }
        }

        $data = $this->model->findPaginatedFiltered($page, $limit, $search, $categoryId, $status);
        $this->json(['data' => $data['items'], 'pagination' => $data['pagination']]);
    }

    public function show(int $id): void
    {
        $statusParam = trim((string) filter_input(INPUT_GET, 'status', FILTER_UNSAFE_RAW));
        $status = null;
        if (in_array($statusParam, ['draft', 'published'], true)) {
            $status = $statusParam;
        } elseif ($statusParam !== 'all') {
            $status = 'published';
        }

        $article = $this->model->findDetailedById($id, $status);
        if ($article === null) {
            $this->json(['error' => 'Article introuvable.'], 404);
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
