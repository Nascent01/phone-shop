<?php

namespace App\Console\Commands\Products;

use App\Constants\Attribute\AttributeConstant;
use App\Services\Attribute\AttributeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportAttributes extends Command
{
    protected $attributeService;

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
    protected $description = 'Create attributes from constant';

    public function __construct(AttributeService $attributeService)
    {
        parent::__construct();
        $this->attributeService = $attributeService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true);
        $this->info('Products attributes import has started');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('attributes')->truncate();
        DB::table('attribute_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::disableQueryLog();

        $dataToInsertAttributesTranslations = [];

        $locales = config('app.locales');

        foreach (AttributeConstant::ATTRIBUTES_ARRAY as $attribute) {
            $attributeNew = $this->attributeService->create([
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($locales as $locale) {
                $dataToInsertAttributesTranslations[] = [
                    'attribute_id' => $attributeNew->id,
                    'locale' => $locale,
                    'slug' => str_slug($attribute),
                    'name' => $attribute,
                ];
            }
        }

        if (!empty($dataToInsertAttributesTranslations) && count($dataToInsertAttributesTranslations) > 0) {
            $this->attributeService->insertTranslation($dataToInsertAttributesTranslations);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $this->info('Products attributes import task has finished for: ' . $executionTime);
    }
}
