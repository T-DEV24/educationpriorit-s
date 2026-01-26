<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Config/auth.php';

class UsersController extends BaseController
{
    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function index(): void
    {
        $me = filter_input(INPUT_GET, 'me', FILTER_VALIDATE_BOOLEAN);
        if ($me) {
            $userId = AuthSession::requireUserId(function (): void {
                $this->json(['error' => 'Connexion requise.'], 401);
            });
            if ($userId === null) {
                return;
            }

            $user = $this->model->find($userId);
            if ($user === null) {
                $this->json(['error' => 'Utilisateur introuvable.'], 404);
                return;
            }

            $this->json(['data' => $this->sanitizeUser($user)]);
            return;
        }

        parent::index();
    }

    public function show(int $id): void
    {
        $userId = AuthSession::requireUserId(function (): void {
            $this->json(['error' => 'Connexion requise.'], 401);
        });
        if ($userId === null) {
            return;
        }

        if (! AuthSession::isAdmin() && $userId !== $id) {
            $this->json(['error' => 'Accès non autorisé.'], 403);
            return;
        }

        $user = $this->model->find($id);
        if ($user === null) {
            $this->json(['error' => 'Utilisateur introuvable.'], 404);
            return;
        }

        $this->json(['data' => $this->sanitizeUser($user)]);
    }

    public function update(int $id): void
    {
        $userId = AuthSession::requireUserId(function (): void {
            $this->json(['error' => 'Connexion requise.'], 401);
        });
        if ($userId === null) {
            return;
        }

        if (! AuthSession::isAdmin() && $userId !== $id) {
            $this->json(['error' => 'Accès non autorisé.'], 403);
            return;
        }

        parent::update($id);
    }

    private function sanitizeUser(array $user): array
    {
        unset($user['password_hash']);
        return $user;
    }
}
