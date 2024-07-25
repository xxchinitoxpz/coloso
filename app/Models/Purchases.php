<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Purchases extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;
    protected $fillable = ['total'];

    public function products()
    {
        return $this->belongsToMany(Products::class, 'product_purchase', 'purchase_id', 'product_id')
                    ->withPivot('quantity', 'subtotal')
                    ->withTimestamps();
    }
}
