<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Tariff extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;
    protected $fillable =["schedule","price","court_id"];

    // Relación con la tabla 'court'
    public function court()
    {
        return $this->belongsTo(Court::class, 'court_id');
    }
}
