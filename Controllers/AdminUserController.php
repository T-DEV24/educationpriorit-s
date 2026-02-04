<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Config/auth.php';

class AdminUserController extends BaseController
{
    public function __construct()
    {
        $this->model = new UserModel();
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

        $payload = $this->preparePayload($this->getRequestData());
        if ($payload === []) {
            $this->json(['error' => 'Données invalides ou manquantes.'], 422);
            return;
        }

        if (! isset($payload['password_hash']) || $payload['password_hash'] === '') {
            $this->json(['error' => 'Mot de passe requis.'], 422);
            return;
        }

        $created = $this->model->create($payload);
        if ($created === null) {
            $this->json(['error' => 'Aucune donnée autorisée fournie.'], 422);
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

        $payload = $this->preparePayload($this->getRequestData());
        if ($payload === []) {
            $this->json(['error' => 'Données invalides ou manquantes.'], 422);
            return;
        }

        $updated = $this->model->update($id, $payload);
        if ($updated === null) {
            $this->json(['error' => 'Aucune donnée autorisée fournie.'], 422);
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

    private function preparePayload(array $payload): array
    {
        if (isset($payload['password']) && $payload['password'] !== '') {
            $payload['password_hash'] = password_hash((string) $payload['password'], PASSWORD_DEFAULT);
        }

        unset($payload['password']);

        if (! isset($payload['role_id'])) {
            $payload['role_id'] = 2;
        }

        if (! isset($payload['is_active'])) {
            $payload['is_active'] = 1;
        }

        return $payload;
    }
}
