<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel
{
    protected string $table = 'users';
    protected array $fillable = [
        'role_id',
        'full_name',
        'email',
        'password_hash',
        'is_active',
    ];
}
