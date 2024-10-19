<?php

namespace App\Console\Commands\Products;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportAttributesChoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:import-attributes-choices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import attributes choices from json file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true);
        $this->info('Attributes choices import has started');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('attribute_choices')->truncate();
        DB::table('attribute_choices_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::disableQueryLog();

        $locales = config('app.locales');

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $this->info('Attributes choices import task has finished for: ' . $executionTime);
    }
}
