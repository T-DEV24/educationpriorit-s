<?php

declare(strict_types=1);

require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Config/auth.php';

class AuthController
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function register(): void
    {
        $payload = $this->getRequestData();
        $fullName = trim((string) ($payload['full_name'] ?? ''));
        $email = trim((string) ($payload['email'] ?? ''));
        $password = (string) ($payload['password'] ?? '');

        if ($fullName === '' || $email === '' || $password === '') {
            $this->json(['error' => 'Nom complet, email et mot de passe requis.'], 422);
            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['error' => 'Adresse email invalide.'], 422);
            return;
        }

        if ($this->users->findByEmail($email) !== null) {
            $this->json(['error' => 'Cet email est déjà utilisé.'], 409);
            return;
        }

        $data = [
            'role_id' => 2,
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'is_active' => 1,
        ];

        $created = $this->users->createUser($data);
        if ($created === null) {
            $this->json(['error' => 'Impossible de créer le compte.'], 500);
            return;
        }

        AuthSession::setUser($created);

        $token = AuthSession::generateJwt($created);
        $this->json(['data' => $created, 'token' => $token], 201);
    }

    public function login(): void
    {
        $payload = $this->getRequestData();
        $email = trim((string) ($payload['email'] ?? ''));
        $password = (string) ($payload['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->json(['error' => 'Email et mot de passe requis.'], 422);
            return;
        }

        $user = $this->users->findByEmail($email);
        if ($user === null || ! password_verify($password, $user['password_hash'] ?? '')) {
            $this->json(['error' => 'Identifiants invalides.'], 401);
            return;
        }

        if ((int) ($user['is_active'] ?? 1) !== 1) {
            $this->json(['error' => 'Compte désactivé.'], 403);
            return;
        }

        AuthSession::setUser($user);

        $token = AuthSession::generateJwt($user);
        $this->json(['message' => 'Connexion réussie.', 'data' => $user, 'token' => $token]);
    }

    public function logout(): void
    {
        AuthSession::logout();

        $this->json(['message' => 'Déconnexion réussie.']);
    }

    public function updatePassword(): void
    {
        $userId = AuthSession::requireUserId(function (): void {
            $this->json(['error' => 'Connexion requise.'], 401);
        });
        if ($userId === null) {
            return;
        }

        $payload = $this->getRequestData();
        $current = (string) ($payload['current_password'] ?? '');
        $new = (string) ($payload['new_password'] ?? '');

        if ($current === '' || $new === '') {
            $this->json(['error' => 'Mot de passe actuel et nouveau requis.'], 422);
            return;
        }

        $user = $this->users->find($userId);
        if ($user === null || ! password_verify($current, $user['password_hash'] ?? '')) {
            $this->json(['error' => 'Mot de passe actuel incorrect.'], 403);
            return;
        }

        $updated = $this->users->updatePasswordHash($userId, password_hash($new, PASSWORD_DEFAULT));
        if (! $updated) {
            $this->json(['error' => 'Impossible de mettre à jour le mot de passe.'], 500);
            return;
        }

        $this->json(['message' => 'Mot de passe mis à jour.']);
    }

    private function getRequestData(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $input = file_get_contents('php://input');
            $decoded = json_decode($input ?: '', true);

            return is_array($decoded) ? $decoded : [];
        }

        return $_POST ?: [];
    }

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
