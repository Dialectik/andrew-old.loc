<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtitle extends Model
{
    use HasFactory;

    // Связь с моделью Page
    public function page()
    {
        return $this->belongsTo(Page::class, 'id', 'page_id');
    }
}
