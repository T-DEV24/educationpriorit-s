<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class LikeModel extends BaseModel
{
    protected string $table = 'likes';
    protected array $fillable = [
        'article_id',
        'user_id',
    ];

    public function findByUserAndArticle(int $userId, int $articleId): ?array
    {
        $sql = sprintf('SELECT * FROM %s WHERE user_id = :user_id AND article_id = :article_id LIMIT 1', $this->table);
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }

    public function countByArticle(int $articleId): int
    {
        $sql = sprintf('SELECT COUNT(*) FROM %s WHERE article_id = :article_id', $this->table);
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}
