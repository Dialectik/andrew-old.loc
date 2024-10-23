<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $table = 'transloc';
    protected $fillable = ['locale', 'key', 'value'];

    public static $addLocalesArray = [
        'uk',
        'en',
    ];
}
