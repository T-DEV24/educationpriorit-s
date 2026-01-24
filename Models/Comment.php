<?php

class Comment
{
    public static function byUser(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT comments.*, articles.title FROM comments JOIN articles ON articles.id = comments.article_id WHERE comments.user_id = :user_id ORDER BY comments.created_at DESC');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT comments.*, users.full_name, articles.title FROM comments JOIN users ON users.id = comments.user_id JOIN articles ON articles.id = comments.article_id ORDER BY comments.created_at DESC');
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $commentId, int $isApproved): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE comments SET is_approved = :is_approved WHERE id = :id');
        $stmt->execute(['is_approved' => $isApproved, 'id' => $commentId]);
    }

    public static function delete(int $commentId): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM comments WHERE id = :id');
        $stmt->execute(['id' => $commentId]);
    }
}
