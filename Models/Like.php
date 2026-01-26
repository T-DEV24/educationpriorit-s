<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Like extends BaseModel
{
    protected string $table = 'likes';
    protected array $fillable = [
        'article_id',
        'user_id',
    ];
}
