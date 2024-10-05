<?php

namespace App\Console\Commands\Products;

use App\Models\ProductCategory\ProductCategory;
use App\Models\ProductCategory\ProductCategoryTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportProductsCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-products-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products categories from json file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true);
        $this->info('Products Categories task has started');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('product_categories')->truncate();
        DB::table('product_category_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::disableQueryLog();

        $categoriesPath = storage_path('Categories.json');
        $productCategories = json_decode(file_get_contents($categoriesPath));

        $locales = config('app.locales');

        foreach ($productCategories as $productCategory) {
            $productCategoryNew = new ProductCategory();
            $productCategoryNew->created_at = now();
            $productCategoryNew->updated_at= now();
            $productCategoryNew->save();

            foreach ($locales as $locale) {
                ProductCategoryTranslation::insert([
                    'product_category_id' => $productCategoryNew->id,
                    'locale' => $locale,
                    'name' => $productCategory->Cell_Phone_Brand,
                ]);
            }
        }
        
        $scriptTimeEnd = microtime(true);
        $scriptExecuteTime = ($scriptTimeEnd - $startTime) / 60;
        $this->info('Products Categories task has finished for - ' . $scriptExecuteTime);
    }
}
