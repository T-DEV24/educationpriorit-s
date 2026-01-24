<?php

class PdfEdition
{
    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT * FROM pdf_editions ORDER BY published_at DESC, created_at DESC');
        return $stmt->fetchAll();
    }

    public static function latest(): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT * FROM pdf_editions ORDER BY published_at DESC, created_at DESC LIMIT 1');
        $edition = $stmt->fetch();
        return $edition ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM pdf_editions WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $edition = $stmt->fetch();
        return $edition ?: null;
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM pdf_editions WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $edition = $stmt->fetch();
        return $edition ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO pdf_editions (title, slug, description, price, cover_path, pdf_path, published_at) VALUES (:title, :slug, :description, :price, :cover_path, :pdf_path, :published_at)');
        $stmt->execute([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'price' => $data['price'],
            'cover_path' => $data['cover_path'],
            'pdf_path' => $data['pdf_path'],
            'published_at' => $data['published_at'],
        ]);
        return (int) $pdo->lastInsertId();
    }
}
