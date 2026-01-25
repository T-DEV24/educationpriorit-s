<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Download extends BaseModel
{
    protected string $table = 'downloads';
    protected array $fillable = [
        'user_id',
        'pdf_edition_id',
        'downloaded_at',
    ];
}
