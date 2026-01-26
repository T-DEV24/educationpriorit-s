<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class TagModel extends BaseModel
{
    protected string $table = 'tags';
    protected array $fillable = [
        'name',
        'slug',
    ];
}
