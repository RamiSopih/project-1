<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable=[
        'final_price'
    ];

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
    public function select_place()
    {
        return $this->hasOne(Select_place::class);
    }
    


    public $timestamps = false;
    use HasFactory;
}
