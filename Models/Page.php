<?php

class Page
{
    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT * FROM pages ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO pages (title, slug, content, is_published) VALUES (:title, :slug, :content, :is_published)');
        $stmt->execute([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'],
            'is_published' => $data['is_published'],
        ]);
        return (int) $pdo->lastInsertId();
    }
}
