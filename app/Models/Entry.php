<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Entry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date_expire',
        'to_buy',
        'amount'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
