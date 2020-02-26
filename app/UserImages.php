<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserImages extends Model
{
    protected $table = 'user_images';
    protected $fillable = [
        'id','userId', 'parkId','comingImage','outgoingImage','comingTime','outgoingTime'
    ];
}
