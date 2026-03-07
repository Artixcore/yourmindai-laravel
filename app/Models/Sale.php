<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'sale_number',
        'customer_name',
        'customer_email',
        'sale_date',
        'status',
        'notes',
        'subtotal',
        'tax',
        'total',
        'invoice_path',
        'created_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Sale $sale) {
            if (empty($sale->sale_number)) {
                $sale->sale_number = self::generateSaleNumber();
            }
        });
    }

    public static function generateSaleNumber(): string
    {
        $prefix = 'SAL-' . date('Ymd') . '-';
        $last = self::where('sale_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        $seq = $last ? (int) substr($last->sale_number, -3) + 1 : 1;
        return $prefix . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_items')
            ->withPivot('quantity', 'unit_price', 'total')
            ->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
