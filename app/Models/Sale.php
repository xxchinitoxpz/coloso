<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Sale extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;
    protected $fillable =["total","balance","final_payment_date","state","user_id","customer_id"];

    public function products()
    {
        return $this->belongsToMany(Products::class, 'product_sales', 'sale_id', 'product_id')
                    ->withPivot('quantity', 'subtotal')
                    ->withTimestamps();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getCustomerDebtAttribute()
    {
        $debt = $this->total - $this->balance;
        return "{$this->customer->name} | Deuda: {$debt}";
    }

}
