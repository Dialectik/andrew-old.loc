<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avmedia extends Model
{
    use HasFactory;

    /**
     * 
     */
    public function page()
    {
        return $this->belongsTo(Page::class, 'id', 'page_id');
    }

    
    /**
     * Фильтр аудио
     */
    public function scopeAudio($query)
    {
        return $query->where('type', 'AUDIO');
    }

    /**
     * Фильтр видео
     */
    public function scopeVideo($query)
    {
        return $query->where('type', 'VIDEO');
    }

}
