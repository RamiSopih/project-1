<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catigory extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'item_name',
        'item_type',
        'image',
        'price'
    ];

    public function cart(){
        return $this->hasMany(Cart::class);
    }
    public function users(){
        return $this->belongsToMany(User::class,'user_favs');
    }
    public $timestamps = false;
}
