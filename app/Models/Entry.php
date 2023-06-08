<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class wpis extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date_added',
        'date_expire',
        'to_buy',
        'amount'
    ];
}
