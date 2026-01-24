<?php

class Download
{
    public static function log(int $userId, int $pdfId): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO downloads (user_id, pdf_edition_id) VALUES (:user_id, :pdf_id)');
        $stmt->execute(['user_id' => $userId, 'pdf_id' => $pdfId]);
    }
}
