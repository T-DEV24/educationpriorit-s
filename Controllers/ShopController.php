<?php

declare(strict_types=1);

require_once __DIR__ . '/../Models/PdfEditionModel.php';
require_once __DIR__ . '/../Models/OrderModel.php';
require_once __DIR__ . '/../Models/DownloadModel.php';
require_once __DIR__ . '/../Config/auth.php';

class ShopController
{
    private PdfEditionModel $pdfs;
    private OrderModel $orders;
    private DownloadModel $downloads;

    public function __construct()
    {
        $this->pdfs = new PdfEditionModel();
        $this->orders = new OrderModel();
        $this->downloads = new DownloadModel();
    }

    public function listPdf(): void
    {
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 12;

        $data = $this->pdfs->findPaginated($page, $limit);
        $this->json(['data' => $data['items'], 'pagination' => $data['pagination']]);
    }

    public function purchase(): void
    {
        $userId = AuthSession::requireUserId(function (): void {
            $this->json(['error' => 'Connexion requise.'], 401);
        });
        if ($userId === null) {
            return;
        }

        $payload = $this->getRequestData();
        $pdfId = (int) ($payload['pdf_edition_id'] ?? 0);

        if ($pdfId <= 0) {
            $this->json(['error' => 'PDF requis.'], 422);
            return;
        }

        $pdf = $this->pdfs->find($pdfId);
        if ($pdf === null) {
            $this->json(['error' => 'PDF introuvable.'], 404);
            return;
        }

        $existing = $this->orders->findPaidByUserAndPdf($userId, $pdfId);
        if ($existing !== null) {
            $this->json(['error' => 'Achat déjà effectué.'], 409);
            return;
        }

        $amount = (int) ($pdf['price'] ?? 0);
        $order = $this->orders->create([
            'user_id' => $userId,
            'pdf_edition_id' => $pdfId,
            'amount' => $amount,
            'status' => 'paid',
        ]);

        if ($order === null) {
            $this->json(['error' => 'Impossible de créer la commande.'], 500);
            return;
        }

        $this->downloads->create([
            'user_id' => $userId,
            'pdf_edition_id' => $pdfId,
            'downloaded_at' => date('Y-m-d H:i:s'),
        ]);

        $this->json(['message' => 'Achat confirmé.', 'data' => $order], 201);
    }

    public function verify(int $pdfId): void
    {
        $userId = AuthSession::requireUserId(function (): void {
            $this->json(['error' => 'Connexion requise.'], 401);
        });
        if ($userId === null) {
            return;
        }

        $pdf = $this->pdfs->find($pdfId);
        if ($pdf === null) {
            $this->json(['error' => 'PDF introuvable.'], 404);
            return;
        }

        $hasPaid = $this->orders->findPaidByUserAndPdf($userId, $pdfId) !== null;
        $hasDownload = $this->downloads->findByUserAndPdf($userId, $pdfId) !== null;

        $this->json([
            'data' => [
                'pdf_edition_id' => $pdfId,
                'has_access' => $hasPaid || $hasDownload,
            ],
        ]);
    }

    public function download(int $pdfId): void
    {
        $userId = AuthSession::requireUserId(function (): void {
            $this->json(['error' => 'Connexion requise.'], 401);
        });
        if ($userId === null) {
            return;
        }

        $pdf = $this->pdfs->find($pdfId);
        if ($pdf === null) {
            $this->json(['error' => 'PDF introuvable.'], 404);
            return;
        }

        $hasPaid = $this->orders->findPaidByUserAndPdf($userId, $pdfId) !== null;
        if (! $hasPaid) {
            $this->json(['error' => 'Achat requis.'], 403);
            return;
        }

        $filePath = $this->resolvePdfPath((string) ($pdf['pdf_path'] ?? ''));
        if ($filePath === null || ! is_file($filePath)) {
            $this->json(['error' => 'Fichier PDF indisponible.'], 404);
            return;
        }

        $this->downloads->create([
            'user_id' => $userId,
            'pdf_edition_id' => $pdfId,
            'downloaded_at' => date('Y-m-d H:i:s'),
        ]);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="edition-' . $pdfId . '.pdf"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
    }

    private function resolvePdfPath(string $path): ?string
    {
        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        $root = dirname(__DIR__);
        $candidate = $root . '/' . ltrim($path, '/');
        $real = realpath($candidate);

        if ($real === false || ! str_starts_with($real, $root)) {
            return null;
        }

        return $real;
    }

    private function getRequestData(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $input = file_get_contents('php://input');
            $decoded = json_decode($input ?: '', true);

            return is_array($decoded) ? $decoded : [];
        }

        return $_POST ?: [];
    }

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
