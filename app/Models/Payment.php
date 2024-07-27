<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Payment extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;
    protected $fillable = ['amount', 'type_payment_id', 'sale_id'];

    public function typePayment()
    {
        return $this->belongsTo(TypePayment::class, 'type_payment_id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
