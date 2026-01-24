<?php

class Media
{
    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT * FROM media ORDER BY uploaded_at DESC');
        return $stmt->fetchAll();
    }

    public static function create(string $path, string $altText): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO media (file_path, alt_text) VALUES (:file_path, :alt_text)');
        $stmt->execute(['file_path' => $path, 'alt_text' => $altText]);
    }
}
