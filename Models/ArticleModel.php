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

    public function findWithTags(int $id): ?array
    {
        $article = $this->find($id);
        if ($article === null) {
            return null;
        }

        $sql = 'SELECT tag_id FROM article_tags WHERE article_id = :article_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':article_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $tagIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $article['tag_ids'] = array_map('intval', $tagIds ?: []);

        return $article;
    }

    public function createWithTags(array $data, array $tagIds): ?array
    {
        $this->db->beginTransaction();
        try {
            $article = $this->create($data);
            if ($article === null) {
                $this->db->rollBack();
                return null;
            }
            $this->syncTags((int) $article['id'], $tagIds);
            $this->db->commit();

            return $article;
        } catch (Throwable $error) {
            $this->db->rollBack();
            return null;
        }
    }

    public function updateWithTags(int $id, array $data, array $tagIds): ?array
    {
        $this->db->beginTransaction();
        try {
            $article = $this->update($id, $data);
            if ($article === null) {
                $this->db->rollBack();
                return null;
            }
            $this->syncTags($id, $tagIds);
            $this->db->commit();

            return $article;
        } catch (Throwable $error) {
            $this->db->rollBack();
            return null;
        }
    }

    private function syncTags(int $articleId, array $tagIds): void
    {
        $cleanIds = array_values(array_unique(array_filter(array_map('intval', $tagIds), static function (int $id): bool {
            return $id > 0;
        })));

        $delete = $this->db->prepare('DELETE FROM article_tags WHERE article_id = :article_id');
        $delete->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        $delete->execute();

        if ($cleanIds === []) {
            return;
        }

        $insert = $this->db->prepare('INSERT INTO article_tags (article_id, tag_id) VALUES (:article_id, :tag_id)');
        foreach ($cleanIds as $tagId) {
            $insert->bindValue(':article_id', $articleId, PDO::PARAM_INT);
            $insert->bindValue(':tag_id', $tagId, PDO::PARAM_INT);
            $insert->execute();
        }
    }
}
