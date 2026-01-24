<?php

class Auth
{
    public static function user(): ?array
    {
        $userId = session_get('user_id');
        if (!$userId) {
            return null;
        }
        return User::findById((int) $userId);
    }

    public static function check(): bool
    {
        return session_get('user_id') !== null;
    }

    public static function login(int $userId): void
    {
        session_set('user_id', $userId);
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id']);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            flash('error', 'Veuillez vous connecter pour accéder à cette page.');
            redirect('/connexion');
        }
    }

    public static function requireAdmin(): void
    {
        $user = self::user();
        if (!$user || ($user['role_name'] ?? '') !== 'Admin') {
            flash('error', 'Accès administrateur requis.');
            redirect('/connexion');
        }
    }
}
