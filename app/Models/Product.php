<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mrp',
        'sale_price',
        'unit',
        'brand',
        'category',
        'bar_code',
        'cgst',
        'sgst',
        'igst',
        'alias',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'mrp' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'cgst' => 'decimal:2',
            'sgst' => 'decimal:2',
            'igst' => 'decimal:2',
        ];
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}
