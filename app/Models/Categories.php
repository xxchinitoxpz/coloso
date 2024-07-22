<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Categories extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;
    protected $fillable =["categorie"];
}
