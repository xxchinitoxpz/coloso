<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Products extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $fillable = [
        'name',
        'purchase_price',
        'sale_price',
        'stock',
        'state',
        'categorie_id',
        'brand_id',
    ];

    // Relación con la tabla 'categories'
    public function categorie()
    {
        return $this->belongsTo(Categories::class, 'categorie_id');
    }

    // Relación con la tabla 'brands'
    public function brand()
    {
        return $this->belongsTo(Brands::class, 'brand_id');
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchases::class, 'product_purchase', 'product_id', 'purchase_id')
                    ->withPivot('quantity', 'subtotal')
                    ->withTimestamps();
    }

    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'product_sales', 'product_id', 'purchase_id')
                    ->withPivot('quantity', 'subtotal')
                    ->withTimestamps();
    }
}
