<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderParam extends Model
{
    protected $table = 'order_params';
    protected $fillable = [
        'id','userId', 'parkId','incomingTime','outgoingTime'
    ];
}
