<?php

class Payment
{
    public static function create(int $orderId, string $provider, string $status = 'initiated', ?string $reference = null): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO payments (order_id, provider, transaction_ref, status, paid_at) VALUES (:order_id, :provider, :transaction_ref, :status, :paid_at)');
        $stmt->execute([
            'order_id' => $orderId,
            'provider' => $provider,
            'transaction_ref' => $reference,
            'status' => $status,
            'paid_at' => $status === 'success' ? date('Y-m-d H:i:s') : null,
        ]);
        return (int) $pdo->lastInsertId();
    }
}
