<?php

namespace App\Console\Commands\Products;

use App\Constants\ProductCategory\ProductCategoryConstant;
use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use App\Models\ProductCategory\ProductCategoryTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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
        $this->info('Products import has started');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('product_categories')->truncate();
        DB::table('product_category_translations')->truncate();
        DB::table('products')->truncate();
        DB::table('product_translations')->truncate();
        DB::table('product_product_category')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::disableQueryLog();

        Artisan::call('db:seed', ['class' => 'Database\Seeders\ProductCategory\ProductCategoryPhoneSeeder']);

        $productsPath = storage_path('Products.json');
        $products = json_decode(file_get_contents($productsPath), true);
        $locales = config('app.locales');

        $dataToInsertProducts = [];
        $dataToInsertProductsTranslations = [];

        /**
         * Process and insert products data
         */
        foreach ($products as $product) {
            $dataToInsertProducts[] = [
                'old_id' => $product['objectId'],
                'sku' => str()->random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($dataToInsertProducts) && count($dataToInsertProducts) > 0) {
            Product::insert($dataToInsertProducts);
        }

        /**
         * Process and insert products translations data
         */

        $productIdsMappedByOldIds = Product::select('id', 'old_id')
            ->pluck('id', 'old_id')
            ->toArray();

        foreach ($products as $product) {
            if ($productIdsMappedByOldIds[$product['objectId']]) {
                if (!empty($product['Model'])) {
                    $productModelWithoutSymbols = str_replace('_', '', $product['Model']);
                    $productTitle = $product['Brand'] . ' ' . $productModelWithoutSymbols;
                    foreach ($locales as $locale) {
                        $dataToInsertProductsTranslations[] = [
                            'product_id' => $productIdsMappedByOldIds[$product['objectId']],
                            'locale' => $locale,
                            'slug' => str_slug($productTitle),
                            'name' => $productTitle,
                        ];
                    }
                }
            }
        }

        $chunkedProductTranslationsData = array_chunk($dataToInsertProductsTranslations, 1500);

        foreach ($chunkedProductTranslationsData as $chunk) {
            ProductTranslation::insert($chunk);
        }

        /**
         * Process and insert products category data
         */

        $productRecords = Product::all();
        $productCategory = ProductCategoryTranslation::where('name', ProductCategoryConstant::TYPE_PHONE_CATEGORY_BG)->first();
        $productProductCategoryData = [];

        foreach ($productRecords as $productRecord) {
            $productProductCategoryData[] = [
                'product_id' => $productRecord->id,
                'product_category_id' => $productCategory->product_category_id,
            ];
        }

        if (!empty($productProductCategoryData) && count($productProductCategoryData) > 0) {
            DB::table('product_product_category')->insert($productProductCategoryData);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $this->info('Products import task has finished for: ' . $executionTime);
    }
}
