<?php

declare(strict_types=1);

require_once __DIR__ . '/../Models/BaseModel.php';

abstract class BaseController
{
    protected BaseModel $model;

    public function index(): void
    {
        $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 100;
        $offset = filter_input(INPUT_GET, 'offset', FILTER_VALIDATE_INT) ?: 0;
        $items = $this->model->findAll($limit, $offset);

        $this->json(['data' => $items]);
    }

    public function show(int $id): void
    {
        $item = $this->model->find($id);
        if ($item === null) {
            $this->json(['error' => 'Ressource introuvable.'], 404);
            return;
        }

        $this->json(['data' => $item]);
    }

    public function store(): void
    {
        $payload = $this->getRequestData();
        if ($payload === []) {
            $this->json(['error' => 'Données invalides ou manquantes.'], 422);
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
        $payload = $this->getRequestData();
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
        $deleted = $this->model->delete($id);
        if (! $deleted) {
            $this->json(['error' => 'Ressource introuvable.'], 404);
            return;
        }

        $this->json(['message' => 'Suppression effectuée.']);
    }

    protected function getRequestData(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $input = file_get_contents('php://input');
            $decoded = json_decode($input ?: '', true);

            return is_array($decoded) ? $decoded : [];
        }

        return $_POST ?: [];
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
