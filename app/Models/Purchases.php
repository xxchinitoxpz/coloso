<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
    protected $fillable = ['total'];

    public function products()
    {
        return $this->belongsToMany(Products::class)->withPivot('quantity', 'subtotal')->withTimestamps();
    }
}
