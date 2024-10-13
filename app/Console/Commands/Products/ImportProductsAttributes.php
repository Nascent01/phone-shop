<?php

namespace App\Console\Commands\Products;

use Illuminate\Console\Command;

class ImportProductsAttributes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:import-products-attributes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products attributes from json file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true);
        $this->info('Products attributes import has started');

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $this->info('Products attributes import task has finished for: ' . $executionTime);
    }
}
