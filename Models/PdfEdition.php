<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class PdfEdition extends BaseModel
{
    protected string $table = 'pdf_editions';
    protected array $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'cover_path',
        'pdf_path',
        'published_at',
    ];
}
