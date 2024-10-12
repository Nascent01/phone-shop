<?php

namespace Database\Seeders\ProductCategory;

use App\Constants\ProductCategory\ProductCategoryConstant;
use App\Models\ProductCategory\ProductCategory;
use App\Models\ProductCategory\ProductCategoryTranslation;
use App\Repository\ProductCategory\ProductCategoryRepository;
use Illuminate\Database\Seeder;

class ProductCategoryPhoneSeeder extends Seeder
{
    protected $productCategoryRepository;

    public function __construct(ProductCategoryRepository $productCategoryRepository)
    {
        $this->productCategoryRepository = $productCategoryRepository;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locales = config('app.locales');

     $productCategory = $this->productCategoryRepository->create([
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($locales as $locale) {
            $this->productCategoryRepository->createTranslation([
                'product_category_id' => $productCategory->id,
                'locale' => $locale,
                'name' => ProductCategoryConstant::TYPE_PHONE_CATEGORY_ARRAY[$locale],
            ]);
        }
    }
}
