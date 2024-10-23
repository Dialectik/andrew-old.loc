<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update translation files with new keys from the views.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Путь к файлам переводов
        $enFilePath = resource_path('lang/en.json');
        $ukFilePath = resource_path('lang/uk.json');

        // Загрузка существующих переводов
        $enTranslations = json_decode(File::get($enFilePath), true);
        $ukTranslations = json_decode(File::get($ukFilePath), true);

        // Получаем ключи из файлов
        $existingKeys = array_keys($enTranslations);

        // Получаем ключи из всех видов (например, из views)
        // Здесь нужно реализовать ваш способ сбора ключей
        // Например, можно использовать preg_match_all для поиска ключей в файлах шаблонов
        $newKeys = $this->findTranslationKeysInViews();

        foreach ($newKeys as $key) {
            if (!in_array($key, $existingKeys)) {
                // Если ключа нет в существующих переводах, добавляем его
                $enTranslations[$key] = ""; // Для английского
                $ukTranslations[$key] = ""; // Для украинского
                $this->info("Added new key: $key");
            }
        }

        // Сохранение обновленных файлов
        File::put($enFilePath, json_encode($enTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        File::put($ukFilePath, json_encode($ukTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info('Translation files updated successfully.');
    }

    protected function findTranslationKeysInViews()
    {
        // Здесь нужно реализовать логику поиска ключей
        // Например, вы можете использовать glob для поиска файлов в папке views
        $viewsPath = resource_path('views');
        $pattern = '/__\(\'([^\']+)\'\)/';
        $newKeys = [];

        $files = File::allFiles($viewsPath);
        foreach ($files as $file) {
            $content = File::get($file);
            preg_match_all($pattern, $content, $matches);
            if (isset($matches[1])) {
                $newKeys = array_merge($newKeys, $matches[1]);
            }
        }

        return array_unique($newKeys); // Убираем дубликаты
    }
}
