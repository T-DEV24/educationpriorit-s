<?php

class Article
{
    public static function latest(int $limit = 6): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT articles.*, categories.name AS category_name, categories.slug AS category_slug, users.full_name AS author_name FROM articles JOIN categories ON categories.id = articles.category_id JOIN users ON users.id = articles.author_id WHERE articles.status = "published" ORDER BY articles.published_at DESC, articles.created_at DESC LIMIT :limit');
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function byCategory(int $categoryId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT articles.*, categories.name AS category_name, categories.slug AS category_slug, users.full_name AS author_name FROM articles JOIN categories ON categories.id = articles.category_id JOIN users ON users.id = articles.author_id WHERE articles.status = "published" AND articles.category_id = :category_id ORDER BY articles.published_at DESC, articles.created_at DESC');
        $stmt->execute(['category_id' => $categoryId]);
        return $stmt->fetchAll();
    }

    public static function findBySlug(string $slug): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT articles.*, categories.name AS category_name, categories.slug AS category_slug, users.full_name AS author_name FROM articles JOIN categories ON categories.id = articles.category_id JOIN users ON users.id = articles.author_id WHERE articles.slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $article = $stmt->fetch();
        return $article ?: null;
    }

    public static function search(string $term): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT articles.*, categories.name AS category_name, categories.slug AS category_slug FROM articles JOIN categories ON categories.id = articles.category_id WHERE articles.status = "published" AND (articles.title LIKE :term OR articles.content LIKE :term) ORDER BY articles.published_at DESC');
        $stmt->execute(['term' => '%' . $term . '%']);
        return $stmt->fetchAll();
    }

    public static function likesCount(int $articleId): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM likes WHERE article_id = :article_id');
        $stmt->execute(['article_id' => $articleId]);
        return (int) $stmt->fetchColumn();
    }

    public static function hasLike(int $articleId, int $userId): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id FROM likes WHERE article_id = :article_id AND user_id = :user_id');
        $stmt->execute(['article_id' => $articleId, 'user_id' => $userId]);
        return (bool) $stmt->fetchColumn();
    }

    public static function addLike(int $articleId, int $userId): void
    {
        if (self::hasLike($articleId, $userId)) {
            return;
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO likes (article_id, user_id) VALUES (:article_id, :user_id)');
        $stmt->execute(['article_id' => $articleId, 'user_id' => $userId]);
    }

    public static function addComment(int $articleId, int $userId, string $content): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO comments (article_id, user_id, content, is_approved) VALUES (:article_id, :user_id, :content, 0)');
        $stmt->execute([
            'article_id' => $articleId,
            'user_id' => $userId,
            'content' => $content,
        ]);
    }

    public static function comments(int $articleId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT comments.*, users.full_name FROM comments JOIN users ON users.id = comments.user_id WHERE comments.article_id = :article_id AND comments.is_approved = 1 ORDER BY comments.created_at DESC');
        $stmt->execute(['article_id' => $articleId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO articles (category_id, author_id, title, slug, summary, content, image_path, status, published_at) VALUES (:category_id, :author_id, :title, :slug, :summary, :content, :image_path, :status, :published_at)');
        $stmt->execute([
            'category_id' => $data['category_id'],
            'author_id' => $data['author_id'],
            'title' => $data['title'],
            'slug' => $data['slug'],
            'summary' => $data['summary'],
            'content' => $data['content'],
            'image_path' => $data['image_path'],
            'status' => $data['status'],
            'published_at' => $data['published_at'],
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT articles.*, categories.name AS category_name, categories.slug AS category_slug, users.full_name AS author_name FROM articles JOIN categories ON categories.id = articles.category_id JOIN users ON users.id = articles.author_id ORDER BY articles.created_at DESC');
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $articleId, string $status): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE articles SET status = :status, published_at = IF(:status = "published", NOW(), published_at) WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $articleId]);
    }
}
