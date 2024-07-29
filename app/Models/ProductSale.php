<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ProductSale extends Model
{
    use HasFactory;
    use HasFactory, AsSource, Filterable, Attachable;
    protected $fillable = ['product_id','sale_id','quantity','subtotal'];
}
