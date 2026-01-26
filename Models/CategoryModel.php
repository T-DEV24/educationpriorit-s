<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class CategoryModel extends BaseModel
{
    protected string $table = 'categories';
    protected array $fillable = [
        'name',
        'slug',
    ];

    public function findPaginated(int $page = 1, int $limit = 20): array
    {
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));
        $offset = ($page - 1) * $limit;

        $countSql = sprintf('SELECT COUNT(*) FROM %s', $this->table);
        $total = (int) $this->db->query($countSql)->fetchColumn();

        $items = $this->findAll($limit, $offset);

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

    public function findPaginatedWithCounts(int $page = 1, int $limit = 20, ?string $articleStatus = 'published'): array
    {
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));
        $offset = ($page - 1) * $limit;

        $countSql = sprintf('SELECT COUNT(*) FROM %s', $this->table);
        $total = (int) $this->db->query($countSql)->fetchColumn();

        $filters = [];
        $params = [];
        if ($articleStatus !== null) {
            $filters[] = 'articles.status = :status';
            $params[':status'] = $articleStatus;
        }
        $joinFilter = $filters !== [] ? ' AND ' . implode(' AND ', $filters) : '';

        $sql = sprintf(
            'SELECT categories.*,
                COUNT(articles.id) AS article_count
            FROM %s AS categories
            LEFT JOIN articles ON articles.category_id = categories.id%s
            GROUP BY categories.id
            ORDER BY categories.name ASC
            LIMIT :limit OFFSET :offset',
            $this->table,
            $joinFilter
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

    public function findBySlug(string $slug): ?array
    {
        $sql = sprintf('SELECT * FROM %s WHERE slug = :slug LIMIT 1', $this->table);
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }
}
