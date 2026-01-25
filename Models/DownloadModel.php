<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class DownloadModel extends BaseModel
{
    protected string $table = 'downloads';
    protected array $fillable = [
        'user_id',
        'pdf_edition_id',
        'downloaded_at',
    ];

    public function findByUserAndPdf(int $userId, int $pdfEditionId): ?array
    {
        $sql = sprintf('SELECT * FROM %s WHERE user_id = :user_id AND pdf_edition_id = :pdf_id LIMIT 1', $this->table);
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':pdf_id', $pdfEditionId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }
}
