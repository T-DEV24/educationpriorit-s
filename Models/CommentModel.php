<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class CommentModel extends BaseModel
{
    protected string $table = 'comments';
    protected array $fillable = [
        'article_id',
        'user_id',
        'content',
        'is_approved',
    ];
}
