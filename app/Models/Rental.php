<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Rental extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;
    protected $fillable = ['total', 'state', 'start_time', 'total_hours', 'end_time', 'user_id', 'customer_id', 'court_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

}
