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
        return $this->findPaginatedFiltered($page, $limit, null, null, null);
    }

    public function findPaginatedFiltered(
        int $page = 1,
        int $limit = 12,
        ?string $search = null,
        ?int $categoryId = null,
        ?string $status = 'published'
    ): array {
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));
        $offset = ($page - 1) * $limit;

        $filters = [];
        $params = [];

        if ($status !== null) {
            $filters[] = 'articles.status = :status';
            $params[':status'] = $status;
        }

        if ($categoryId !== null) {
            $filters[] = 'articles.category_id = :category_id';
            $params[':category_id'] = $categoryId;
        }

        if ($search !== null && $search !== '') {
            $filters[] = '(articles.title LIKE :search OR articles.summary LIKE :search OR articles.content LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        $whereSql = $filters !== [] ? 'WHERE ' . implode(' AND ', $filters) : '';

        $countSql = sprintf('SELECT COUNT(*) FROM %s %s', $this->table, $whereSql);
        $countStmt = $this->db->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $sql = sprintf(
            'SELECT articles.*,
                categories.name AS category_name,
                categories.slug AS category_slug,
                (SELECT COUNT(*) FROM likes WHERE likes.article_id = articles.id) AS likes_count,
                (SELECT COUNT(*) FROM comments WHERE comments.article_id = articles.id AND comments.is_approved = 1) AS comments_count
            FROM %s AS articles
            LEFT JOIN categories ON categories.id = articles.category_id
            %s
            ORDER BY articles.published_at DESC, articles.created_at DESC
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

    public function findBySlug(string $slug, ?string $status = 'published'): ?array
    {
        $filters = ['articles.slug = :slug'];
        $params = [':slug' => $slug];

        if ($status !== null) {
            $filters[] = 'articles.status = :status';
            $params[':status'] = $status;
        }

        $sql = sprintf(
            'SELECT articles.*,
                categories.name AS category_name,
                categories.slug AS category_slug,
                (SELECT COUNT(*) FROM likes WHERE likes.article_id = articles.id) AS likes_count,
                (SELECT COUNT(*) FROM comments WHERE comments.article_id = articles.id AND comments.is_approved = 1) AS comments_count
            FROM %s AS articles
            LEFT JOIN categories ON categories.id = articles.category_id
            WHERE %s
            LIMIT 1',
            $this->table,
            implode(' AND ', $filters)
        );

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }

    public function findDetailedById(int $id, ?string $status = 'published'): ?array
    {
        $filters = ['articles.id = :id'];
        $params = [':id' => $id];

        if ($status !== null) {
            $filters[] = 'articles.status = :status';
            $params[':status'] = $status;
        }

        $sql = sprintf(
            'SELECT articles.*,
                categories.name AS category_name,
                categories.slug AS category_slug,
                (SELECT COUNT(*) FROM likes WHERE likes.article_id = articles.id) AS likes_count,
                (SELECT COUNT(*) FROM comments WHERE comments.article_id = articles.id AND comments.is_approved = 1) AS comments_count
            FROM %s AS articles
            LEFT JOIN categories ON categories.id = articles.category_id
            WHERE %s
            LIMIT 1',
            $this->table,
            implode(' AND ', $filters)
        );

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }
}
