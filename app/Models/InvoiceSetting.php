<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    protected $table = 'invoice_settings';

    protected $fillable = [
        'company_name',
        'address',
        'phone',
        'email',
        'logo_path',
        'signature_image_path',
        'footer_text',
        'tax_rate',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function get(): self
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([
                'company_name' => '',
                'address' => '',
                'phone' => '',
                'email' => '',
                'footer_text' => '',
                'tax_rate' => 0,
            ]);
        }
        return $setting;
    }
}
