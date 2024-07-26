<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Court extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;
    protected $fillable =["court","state","type_court_id"];

    // Relación con la tabla 'type_court'
    public function type_court()
    {
        return $this->belongsTo(TypeCourt::class, 'type_court_id');
    }
}