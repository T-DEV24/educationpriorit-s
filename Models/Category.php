<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Category extends BaseModel
{
    protected string $table = 'categories';
    protected array $fillable = [
        'name',
        'slug',
    ];
}
