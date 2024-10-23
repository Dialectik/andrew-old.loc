<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Translation;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class FindTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:find';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find translation keys in views and add them to the database';

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
        $files = File::allFiles(resource_path('views'));
        $foundKeys = [];

        foreach ($files as $file) {
            $content = $file->getContents();

            preg_match_all('/__\(\'([^\']+)\'\)/', $content, $matches);

            foreach ($matches[1] as $key) {
                foreach (Translation::$addLocalesArray as $key_loc => $locale) {
                    if (!Translation::where('key', $key)->where('locale', $locale)->exists()) {
                        Translation::create([
                            'locale' => $locale,
                            'key' => $key,
                            'value' => '',
                        ]);
                        $this->info("Added missing translation key for '" . $locale . "': $key");
                    }
                }
            }
        }

        $this->info('Translation keys have been updated.');
    }
}
