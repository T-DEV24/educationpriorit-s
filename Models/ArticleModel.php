<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class ArticleModel extends BaseModel
{
    protected string $table = 'articles';
    protected array $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'summary',
        'content',
        'image_path',
        'status',
        'published_at',
    ];

    public function findPaginated(int $page = 1, int $limit = 12): array
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
}
