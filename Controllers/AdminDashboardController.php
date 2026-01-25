<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Config/database.php';

class AdminDashboardController extends BaseController
{
    public function index(): void
    {
        if (! $this->isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        $db = Database::getConnection();
        $tables = [
            'users' => 'users',
            'articles' => 'articles',
            'comments' => 'comments',
            'pdf_editions' => 'pdf_editions',
            'orders' => 'orders',
            'downloads' => 'downloads',
        ];
        $counts = [];

        foreach ($tables as $key => $table) {
            $sql = sprintf('SELECT COUNT(*) FROM %s', $table);
            $counts[$key] = (int) $db->query($sql)->fetchColumn();
        }

        $this->json(['data' => $counts]);
    }

    private function isAdmin(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int) ($_SESSION['role_id'] ?? 0) === 1;
    }
}
