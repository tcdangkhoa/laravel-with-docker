<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wagers extends Model
{
    protected $table = 'wagers';

    protected $fillable = [
        'id',
        'wager_id',
        'buying_price',
        'buyer_id',
        'bought_at'
    ];
}
