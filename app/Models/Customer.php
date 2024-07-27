<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Customer extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;
    protected $fillable =["name","dni","phone"];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
