<?php

declare(strict_types=1);

require_once __DIR__ . '/../Models/UserModel.php';

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

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $data = [
            'role_id' => (int) ($payload['role_id'] ?? 2),
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => $hash,
            'is_active' => 1,
        ];

        $created = $this->users->createUser($data);
        if ($created === null) {
            $this->json(['error' => 'Impossible de créer le compte.'], 500);
            return;
        }

        $this->startSession();
        $_SESSION['user_id'] = $created['id'] ?? null;
        $_SESSION['user_email'] = $created['email'] ?? $email;
        $_SESSION['user_name'] = $created['full_name'] ?? $fullName;

        $this->json(['data' => $created], 201);
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

        $this->startSession();
        $_SESSION['user_id'] = $user['id'] ?? null;
        $_SESSION['user_email'] = $user['email'] ?? $email;
        $_SESSION['user_name'] = $user['full_name'] ?? '';

        $this->json(['message' => 'Connexion réussie.', 'data' => $user]);
    }

    public function logout(): void
    {
        $this->startSession();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'] ?? '/',
                $params['domain'] ?? '',
                (bool) ($params['secure'] ?? false),
                (bool) ($params['httponly'] ?? true)
            );
        }

        session_destroy();

        $this->json(['message' => 'Déconnexion réussie.']);
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
