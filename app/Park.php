<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Park extends Model
{
    protected $table = 'parks';
    protected $fillable = [
        'id','name', 'price','places','occupied_places','longtitude','latitude','description'
    ];
}
