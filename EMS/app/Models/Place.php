<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'location',
        'price',
        'type',
        'likes',
        'desc',//وصف
        'image'


    ];
    public function user()
    {
        return $this->belongsToMany(User::class, 'user_likes');
    }
    public function select_place(){
        return $this->hasMany(Select_place::class);
    }
    public $timestamps = false;
}
