<?php

class User
{
    public static function findById(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT users.*, roles.name AS role_name FROM users JOIN roles ON roles.id = users.role_id WHERE users.id = :id');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT users.*, roles.name AS role_name FROM users JOIN roles ON roles.id = users.role_id WHERE users.email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO users (role_id, full_name, email, password_hash, is_active) VALUES (:role_id, :full_name, :email, :password_hash, :is_active)');
        $stmt->execute([
            'role_id' => $data['role_id'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'is_active' => $data['is_active'] ?? 1,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT users.*, roles.name AS role_name FROM users JOIN roles ON roles.id = users.role_id ORDER BY users.created_at DESC');
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $id, int $isActive): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE users SET is_active = :is_active WHERE id = :id');
        $stmt->execute(['is_active' => $isActive, 'id' => $id]);
    }

    public static function updatePassword(int $id, string $hash): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
        $stmt->execute(['hash' => $hash, 'id' => $id]);
    }

    public static function roleIdByName(string $name): ?int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id FROM roles WHERE name = :name');
        $stmt->execute(['name' => $name]);
        $role = $stmt->fetch();
        return $role ? (int) $role['id'] : null;
    }
}
