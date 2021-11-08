<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wager extends Model
{
    protected $table = 'wager';

    protected $fillable = [
        'id',
        'total_wager_value',
        'odds',
        'selling_percentage',
        'selling_price',
        'current_selling_price',
        'percentage_sold',
        'amount_sold',
        'placed_at'
    ];
}
