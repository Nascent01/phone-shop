<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products and categories from json file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true);  
        Log::info('Products import has started');
            
        $productsPath = storage_path('Data.json');
        
        $products = json_decode(file_get_contents($productsPath), true);

        foreach ($products as $product) {
           dd($product);
        }

        $endTime = microtime(true);   
        $executionTime = $endTime - $startTime;
        Log::info('Products import task has finished for' . $executionTime);
    }
}
