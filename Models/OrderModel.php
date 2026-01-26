<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class OrderModel extends BaseModel
{
    protected string $table = 'orders';
    protected array $fillable = [
        'user_id',
        'pdf_edition_id',
        'amount',
        'status',
    ];

    public function findPaidByUserAndPdf(int $userId, int $pdfEditionId): ?array
    {
        $sql = sprintf(
            'SELECT * FROM %s WHERE user_id = :user_id AND pdf_edition_id = :pdf_id AND status = :status LIMIT 1',
            $this->table
        );
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':pdf_id', $pdfEditionId, PDO::PARAM_INT);
        $stmt->bindValue(':status', 'paid');
        $stmt->execute();

        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }
}
