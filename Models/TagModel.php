<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class TagModel extends BaseModel
{
    protected string $table = 'tags';
    protected array $fillable = [
        'name',
        'slug',
    ];

    public function findPaginatedWithCounts(int $page = 1, int $limit = 20): array
    {
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));
        $offset = ($page - 1) * $limit;

        $countSql = sprintf('SELECT COUNT(*) FROM %s', $this->table);
        $total = (int) $this->db->query($countSql)->fetchColumn();

        $sql = sprintf(
            'SELECT tags.*,
                COUNT(article_tags.article_id) AS article_count
            FROM %s AS tags
            LEFT JOIN article_tags ON article_tags.tag_id = tags.id
            GROUP BY tags.id
            ORDER BY tags.name ASC
            LIMIT :limit OFFSET :offset',
            $this->table
        );

        $stmt = $this->db->prepare($sql);
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
