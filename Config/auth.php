<?php

declare(strict_types=1);

final class AuthSession
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function setUser(array $user): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'] ?? null;
        $_SESSION['user_email'] = $user['email'] ?? '';
        $_SESSION['user_name'] = $user['full_name'] ?? '';
        $_SESSION['role_id'] = (int) ($user['role_id'] ?? 2);
    }

    public static function requireUserId(callable $onError): ?int
    {
        self::start();
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $onError();
            return null;
        }

        return $userId;
    }

    public static function isAdmin(): bool
    {
        self::start();

        return (int) ($_SESSION['role_id'] ?? 0) === 1;
    }

    public static function logout(): void
    {
        self::start();
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
    }
}
