<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/auth.php';

class AdminStatsController extends BaseController
{
    public function index(): void
    {
        if (! AuthSession::isAdmin()) {
            $this->json(['error' => 'Accès administrateur requis.'], 403);
            return;
        }

        $db = Database::getConnection();
        $stats = [];
        $stats['articles_published'] = (int) $db->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
        $stats['users'] = (int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $stats['comments'] = (int) $db->query('SELECT COUNT(*) FROM comments')->fetchColumn();
        $stats['orders_paid'] = (int) $db->query("SELECT COUNT(*) FROM orders WHERE status = 'paid'")->fetchColumn();
        $stats['orders_total'] = (int) $db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
        $stats['downloads'] = (int) $db->query('SELECT COUNT(*) FROM downloads')->fetchColumn();
        $stats['pdf_editions'] = (int) $db->query('SELECT COUNT(*) FROM pdf_editions')->fetchColumn();

        $totalRevenue = (int) $db->query("SELECT COALESCE(SUM(amount), 0) FROM orders WHERE status = 'paid'")->fetchColumn();
        $stats['revenue'] = $totalRevenue;

        $this->json(['data' => $stats]);
    }
}
