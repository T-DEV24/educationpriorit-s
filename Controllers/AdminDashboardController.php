<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/auth.php';

class AdminDashboardController extends BaseController
{
    public function index(): void
    {
        if (! AuthSession::isAdmin()) {
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

}
