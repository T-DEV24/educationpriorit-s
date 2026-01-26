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

    public function findByUserPaginated(int $userId, int $page = 1, int $limit = 10): array
    {
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));
        $offset = ($page - 1) * $limit;

        $countSql = sprintf('SELECT COUNT(*) FROM %s WHERE user_id = :user_id', $this->table);
        $countStmt = $this->db->prepare($countSql);
        $countStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $sql = sprintf(
            'SELECT orders.*,
                pdf_editions.title AS pdf_title,
                pdf_editions.slug AS pdf_slug
            FROM %s AS orders
            LEFT JOIN pdf_editions ON pdf_editions.id = orders.pdf_edition_id
            WHERE orders.user_id = :user_id
            ORDER BY orders.created_at DESC
            LIMIT :limit OFFSET :offset',
            $this->table
        );

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        return [
            'items' => $items,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => (int) ceil($total / $limit),
            ],
        ];
    }
}
