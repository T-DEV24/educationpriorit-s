<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Article extends BaseModel
{
    protected string $table = 'articles';
    protected array $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'summary',
        'content',
        'image_path',
        'status',
        'published_at',
    ];
}
