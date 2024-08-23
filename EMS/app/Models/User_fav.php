<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_fav extends Model
{
    use HasFactory;
    protected $fillable =[
        'user_id',
        'catigory_id',
        'fav_name'
    ];
    public  $timestamps=false;
}
