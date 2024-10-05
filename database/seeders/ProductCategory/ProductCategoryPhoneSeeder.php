<?php

namespace Database\Seeders\ProductCategory;

use App\Constants\ProductCategory\ProductCategoryConstant;
use App\Models\ProductCategory\ProductCategory;
use App\Models\ProductCategory\ProductCategoryTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategoryPhoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $productCategory = ProductCategory::insert([
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $locales = config('app.locales');    

        foreach($locales as $locale) {
            ProductCategoryTranslation::insert([
                'product_category_id' => $productCategory,
                'locale' => $locale,
                'name' => ProductCategoryConstant::TYPE_PHONE_CATEGORY_ARRAY[$locale],
            ]);
        }
    }
}
