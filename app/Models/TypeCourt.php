<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class TypeCourt extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $fillable = [
        'type_court'
    ];

    // Relación con la tabla 'court'
    public function court()
    {
        return $this->hasMany(Court::class, 'type_court_id');
    }
}
