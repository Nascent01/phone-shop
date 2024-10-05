<?php

namespace App\Console\Commands\Products;

use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:import-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed Phone Category and Import products from json file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true);  
        Log::info('Products import has started');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('product_categories')->truncate();
        DB::table('product_category_translations')->truncate();
        DB::table('products')->truncate();
        DB::table('product_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::disableQueryLog();

        Artisan::call('db:seed', ['class' => 'Database\Seeders\ProductCategory\ProductCategoryPhoneSeeder']);

        $productsPath = storage_path('Products.json');
        $products = json_decode(file_get_contents($productsPath), true);

        $locales = config('app.locales');

        foreach ($products as $product) {
            $productModelWithoutSymbols = str_replace('_', '', $product['Model']);
            $productTitle = $product['Brand'] . ' ' . $productModelWithoutSymbols;

           $productnew = Product::insert([
                'sku' => str()->random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach($locales as $locale) {
                ProductTranslation::insert([
                    'product_id' => $productnew,
                    'locale' => $locale,
                    'slug' => str_slug($productTitle),
                    'name' => $productTitle,
                ]);
            }
        }

        $endTime = microtime(true);   
        $executionTime = $endTime - $startTime;
        Log::info('Products import task has finished for' . $executionTime);
    }
}
