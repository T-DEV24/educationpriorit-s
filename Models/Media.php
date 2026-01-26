<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Media extends BaseModel
{
    protected string $table = 'media';
    protected array $fillable = [
        'file_path',
        'alt_text',
    ];
}
