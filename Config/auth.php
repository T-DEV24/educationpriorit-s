<?php

declare(strict_types=1);

require_once __DIR__ . '/jwt.php';

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
        if ($userId > 0) {
            return $userId;
        }

        $token = self::getBearerToken();
        if ($token !== null) {
            $payload = self::decodeJwt($token);
            if ($payload !== null) {
                $userId = (int) ($payload['sub'] ?? 0);
                if ($userId > 0) {
                    $_SESSION['user_id'] = $userId;
                    return $userId;
                }
            }
        }

        $onError();
        return null;
    }

    public static function isAdmin(): bool
    {
        self::start();

        return (int) ($_SESSION['role_id'] ?? 0) === 1;
    }

    public static function generateJwt(array $user): string
    {
        $now = time();
        $payload = [
            'iss' => JWT_ISSUER,
            'iat' => $now,
            'exp' => $now + JWT_TTL_SECONDS,
            'sub' => (int) ($user['id'] ?? 0),
            'role_id' => (int) ($user['role_id'] ?? 2),
        ];

        return self::encodeJwt($payload);
    }

    private static function encodeJwt(array $payload): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $segments = [
            self::base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES)),
            self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES)),
        ];
        $signature = hash_hmac('sha256', implode('.', $segments), JWT_SECRET_KEY, true);
        $segments[] = self::base64UrlEncode($signature);

        return implode('.', $segments);
    }

    private static function decodeJwt(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        [$header64, $payload64, $signature64] = $parts;
        $signature = self::base64UrlDecode($signature64);
        $expected = hash_hmac('sha256', $header64 . '.' . $payload64, JWT_SECRET_KEY, true);
        if (! hash_equals($expected, $signature)) {
            return null;
        }
        $payload = json_decode(self::base64UrlDecode($payload64), true);
        if (! is_array($payload)) {
            return null;
        }
        if (isset($payload['exp']) && time() > (int) $payload['exp']) {
            return null;
        }

        return $payload;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }

    private static function getBearerToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if ($header === '') {
            return null;
        }
        if (preg_match('/Bearer\\s+(\\S+)/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
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
