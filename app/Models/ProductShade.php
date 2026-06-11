<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductShade extends Model
{
    use HasFactory;

    protected $table = 'product_shades';

    protected $fillable = [
        'product_id',
        'shade_name',
        'tone',
        'undertone',
        'hex_color',
        'stock', // ✅ TAMBAH INI
    ];

    protected $casts = [
        'stock' => 'integer', // ✅ biar pasti angka
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}