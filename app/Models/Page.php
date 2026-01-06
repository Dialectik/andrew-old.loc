<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Page extends Model
{
    use HasFactory;

    use Translatable;

    // Перечислите переводимые поля
    protected $translatable = ['title', 'slug', 'meta_description', 'source'];

    // Разрешённые для массового заполнения поля
    // protected $fillable = ['title', 'excerpt', 'body', 'slug', 'status', 'source'];

    public function scopePublished($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    /**
     * Фильтр поэзии
     */
    public function scopePoems($query)
    {
        return $query->where('source', 'POEM');
    }

    /**
     * Фильтр музыки
     */
    public function scopeMusic($query)
    {
        return $query->where('source', 'MUSIC');
    }

    /**
     * 
     */
    public function avmedia()
    {
        return $this->hasMany(Avmedia::class, 'page_id', 'id');
    }

    /**
     * 
     */
    public function subtitles()
    {
        return $this->hasMany(Subtitle::class, 'page_id', 'id');
    }

}
