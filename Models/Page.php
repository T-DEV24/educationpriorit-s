<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Page extends BaseModel
{
    protected string $table = 'pages';
    protected array $fillable = [
        'title',
        'slug',
        'content',
        'is_published',
    ];
}
