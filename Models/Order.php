<?php

class Order
{
    public static function create(int $userId, int $pdfId, int $amount): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO orders (user_id, pdf_edition_id, amount, status) VALUES (:user_id, :pdf_id, :amount, "pending")');
        $stmt->execute([
            'user_id' => $userId,
            'pdf_id' => $pdfId,
            'amount' => $amount,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function markPaid(int $orderId): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE orders SET status = "paid" WHERE id = :id');
        $stmt->execute(['id' => $orderId]);
    }

    public static function hasPaidOrder(int $userId, int $pdfId): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id FROM orders WHERE user_id = :user_id AND pdf_edition_id = :pdf_id AND status = "paid"');
        $stmt->execute(['user_id' => $userId, 'pdf_id' => $pdfId]);
        return (bool) $stmt->fetchColumn();
    }

    public static function listByUser(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT orders.*, pdf_editions.title, pdf_editions.id AS pdf_id, pdf_editions.slug AS pdf_slug FROM orders JOIN pdf_editions ON pdf_editions.id = orders.pdf_edition_id WHERE orders.user_id = :user_id ORDER BY orders.created_at DESC');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT orders.*, users.full_name, pdf_editions.title FROM orders JOIN users ON users.id = orders.user_id JOIN pdf_editions ON pdf_editions.id = orders.pdf_edition_id ORDER BY orders.created_at DESC');
        return $stmt->fetchAll();
    }
}
