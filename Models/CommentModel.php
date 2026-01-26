<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class CommentModel extends BaseModel
{
    protected string $table = 'comments';
    protected array $fillable = [
        'article_id',
        'user_id',
        'content',
        'is_approved',
    ];

    public function findByArticlePaginated(int $articleId, int $page = 1, int $limit = 10, bool $approvedOnly = true): array
    {
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));
        $offset = ($page - 1) * $limit;

        $filters = ['comments.article_id = :article_id'];
        $params = [':article_id' => $articleId];

        if ($approvedOnly) {
            $filters[] = 'comments.is_approved = 1';
        }

        $whereSql = 'WHERE ' . implode(' AND ', $filters);

        $countSql = sprintf('SELECT COUNT(*) FROM %s AS comments %s', $this->table, $whereSql);
        $countStmt = $this->db->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $sql = sprintf(
            'SELECT comments.*,
                users.full_name AS user_name
            FROM %s AS comments
            LEFT JOIN users ON users.id = comments.user_id
            %s
            ORDER BY comments.created_at DESC
            LIMIT :limit OFFSET :offset',
            $this->table,
            $whereSql
        );

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
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
