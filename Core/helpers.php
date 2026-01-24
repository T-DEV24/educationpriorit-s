<?php

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function request_method(): string
{
    return $_SERVER['REQUEST_METHOD'] ?? 'GET';
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function session_get(string $key, $default = null)
{
    return $_SESSION[$key] ?? $default;
}

function session_set(string $key, $value): void
{
    $_SESSION[$key] = $value;
}

function flash(string $key, $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['flash'][$key] ?? null;
    if ($value !== null) {
        unset($_SESSION['flash'][$key]);
    }
    return $value;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return $token !== null && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function is_post(): bool
{
    return request_method() === 'POST';
}
