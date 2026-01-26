<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class PaymentModel extends BaseModel
{
    protected string $table = 'payments';
    protected array $fillable = [
        'order_id',
        'provider',
        'transaction_ref',
        'status',
        'paid_at',
    ];

    public function findByOrder(int $orderId): ?array
    {
        $sql = sprintf('SELECT * FROM %s WHERE order_id = :order_id LIMIT 1', $this->table);
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }
}
