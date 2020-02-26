<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserParam extends Model
{
    protected $table = 'userParam';
    protected $fillable = [
        'userId', 'name','carModel','carNumber',
    ];
}
