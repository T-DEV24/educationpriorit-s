<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Order extends BaseModel
{
    protected string $table = 'orders';
    protected array $fillable = [
        'user_id',
        'pdf_edition_id',
        'amount',
        'status',
    ];
}
