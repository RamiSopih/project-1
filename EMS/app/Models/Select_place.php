<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Select_place extends Model
{
    protected $fillable = [
        'price',
        'name'
    ];

    use HasFactory;

    public function bill()
    {
        return $this->hasOne(Bill::class);
    }
    public function user()
    {
        return $this->hasOne(User::class);
    }
    public function place()
    {
        return $this->belongsTo(Place::class);
    }


    public $timestamps=false;
}
