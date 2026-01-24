<?php

class Category
{
    public static function allWithCounts(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT categories.*, COUNT(articles.id) AS article_count FROM categories LEFT JOIN articles ON articles.category_id = categories.id AND articles.status = "published" GROUP BY categories.id ORDER BY categories.name');
        return $stmt->fetchAll();
    }

    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT * FROM categories ORDER BY name');
        return $stmt->fetchAll();
    }

    public static function findBySlug(string $slug): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $category = $stmt->fetch();
        return $category ?: null;
    }

    public static function create(string $name, string $slug): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO categories (name, slug) VALUES (:name, :slug)');
        $stmt->execute(['name' => $name, 'slug' => $slug]);
    }
}
