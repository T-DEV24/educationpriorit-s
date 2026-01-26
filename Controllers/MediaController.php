<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Media.php';

class MediaController extends BaseController
{
    public function __construct()
    {
        $this->model = new Media();
    }

    public function upload(): void
    {
        $file = $_FILES['file'] ?? null;
        if (! is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->json(['error' => 'Fichier requis.'], 422);
            return;
        }

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
        ];
        $mime = mime_content_type($file['tmp_name']);
        if (! isset($allowed[$mime])) {
            $this->json(['error' => 'Type de fichier non autorisé.'], 422);
            return;
        }

        $uploadsDir = dirname(__DIR__) . '/uploads';
        if (! is_dir($uploadsDir) && ! mkdir($uploadsDir, 0777, true) && ! is_dir($uploadsDir)) {
            $this->json(['error' => 'Impossible de créer le dossier de téléchargement.'], 500);
            return;
        }

        $ext = $allowed[$mime];
        $filename = uniqid('media_', true) . '.' . $ext;
        $target = $uploadsDir . '/' . $filename;

        if (! move_uploaded_file($file['tmp_name'], $target)) {
            $this->json(['error' => 'Impossible de sauvegarder le fichier.'], 500);
            return;
        }

        $record = $this->model->create([
            'file_path' => 'uploads/' . $filename,
            'alt_text' => $_POST['alt_text'] ?? '',
        ]);

        if ($record === null) {
            $this->json(['error' => 'Impossible d’enregistrer le média.'], 500);
            return;
        }

        $this->json(['data' => $record], 201);
    }
}
