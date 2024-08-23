<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_like extends Model
{
    protected $fillable =[
        'user_id',
        'place_id'
    ];
    use HasFactory;
    public $timestamps =false;
}
