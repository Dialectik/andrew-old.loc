<?php

namespace App\Services;

use Illuminate\Translation\FileLoader;
use Illuminate\Support\Facades\Log;
use App\Models\Translation;

class DatabaseLoader extends FileLoader
{
    
    public function __construct($files, $path)
    {
        Log::info('DatabaseLoader initialized.');
        parent::__construct($files, $path);
    }

    public function load($locale, $group, $namespace = null)
    {
        Log::info("DatabaseLoader::load called with locale: {$locale}, group: {$group}, namespace: {$namespace}");
        
        // Загружаем стандартные переводы из файлов
        $lines = parent::load($locale, $group, $namespace);

        // Загружаем переводы из базы данных
        $dbTranslations = Translation::where('locale', $locale)
            ->whereIn('key', array_keys($lines))
            ->pluck('value', 'key')
            ->toArray();

        // Логи
        Log::info("Loaded lines: " . json_encode($lines));
        Log::info("Database translations: " . json_encode($dbTranslations));

        return array_merge($lines, $dbTranslations);
    }
}
