<?php

namespace App\Console\Commands\Products;

use App\Constants\Attribute\AttributeConstant;
use App\Models\Attribute\AttributeTranslation;
use App\Models\AttributeChoice\AttributeChoice;
use App\Repository\AttributeChoice\AttributeChoiceRepository;
use App\Services\AttributeChoice\AttributeChoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportAttributesChoices extends Command
{
    protected $attributeChoiceService, $attributeChoiceRepository;

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

    public function __construct(AttributeChoiceService $attributeChoiceService, AttributeChoiceRepository $attributeChoiceRepository)
    {
        parent::__construct();
        $this->attributeChoiceService = $attributeChoiceService;
        $this->attributeChoiceRepository = $attributeChoiceRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true);
        $this->info('Attributes choices import has started');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('attribute_choices')->truncate();
        DB::table('attribute_choice_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::disableQueryLog();

        $productsPath = storage_path('Products.json');
        $products = json_decode(file_get_contents($productsPath), true);

        $locales = config('app.locales');

        try {
            $attributeIdsMappedByName = DB::table('attribute_translations')
                ->join('attributes', 'attributes.id', '=', 'attribute_translations.attribute_id')
                ->pluck('attributes.id', 'attribute_translations.name')
                ->toArray();

            $attributesChoicesInsertData = [];
            $existingChoices = [];

            /**
             * Process and insert attribute choices data
             */

            foreach ($products as $product) {
                foreach ($product as $attributeName => $attributeChoiceValue) {
                    if (isset($attributeIdsMappedByName[$attributeName]) && !empty($attributeChoiceValue)) {
                        if (!isset($existingChoices[$attributeName])) {
                            $existingChoices[$attributeName] = [];
                        }

                        if ($attributeName == AttributeConstant::TYPE_ATTRIBUTE_COLORS) {
                            $choicesArray = explode('|', $attributeChoiceValue);
                        } elseif ($attributeName == AttributeConstant::TYPE_ATTRIBUTE_INTERNAL_MEMORY) {
                            $choicesArray = explode('/', $attributeChoiceValue);
                        } else {
                            $choicesArray = [$attributeChoiceValue];
                        }

                        foreach ($choicesArray as $choice) {
                            $choice = trim($choice);
                            $machineName = str_slug($choice);

                            if (!empty($machineName) && !in_array($machineName, $existingChoices[$attributeName])) {
                                $existingChoices[$attributeName][] = $machineName;
                            }
                        }
                    }
                }
            }

            foreach ($existingChoices as $attributeName => $choices) {
                foreach ($choices as $machineName) {
                    $attributesChoicesInsertData[] = [
                        'attribute_id' => $attributeIdsMappedByName[$attributeName],
                        'machine_name' => $machineName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            $chunkAttributesChoicesInsertData = array_chunk($attributesChoicesInsertData, 1500);

            foreach ($chunkAttributesChoicesInsertData as $chunk) {
                $this->attributeChoiceService->insert($chunk);
            }

            /**
             * Process and insert attribute choices translations data
             */

            $attributeChoicesIdsMappedByMachineName = AttributeChoice::select('id', 'machine_name')
                ->pluck('id', 'machine_name')
                ->toArray();

            $dataToInsertAttributesChoicesTranslations = [];

            $existingAttributesChoicesTranslations = [];

            foreach ($products as $product) {
                foreach ($product as $attributeName => $attributeChoiceValue) {
                    if (isset($attributeIdsMappedByName[$attributeName]) && !empty($attributeChoiceValue)) {
                        if ($attributeName == AttributeConstant::TYPE_ATTRIBUTE_COLORS) {
                            $choicesTranslationArray = explode('|', $attributeChoiceValue);
                        } elseif ($attributeName == AttributeConstant::TYPE_ATTRIBUTE_INTERNAL_MEMORY) {
                            $choicesTranslationArray = explode('/', $attributeChoiceValue);
                        } else {
                            $choicesTranslationArray = [$attributeChoiceValue];
                        }

                        foreach ($choicesTranslationArray as $choiceTranslation) {
                            $choiceTranslation = trim($choiceTranslation);
                            $machineName = str_slug($choiceTranslation);

                            if (!empty($machineName) && isset($attributeChoicesIdsMappedByMachineName[$machineName])) {
                                $attributeChoiceId = $attributeChoicesIdsMappedByMachineName[$machineName];

                                foreach ($locales as $locale) {
                                    $uniqueKey = $attributeChoiceId . '_' . $locale;
                                    if (!isset($existingAttributesChoicesTranslations[$uniqueKey])) {
                                        $dataToInsertAttributesChoicesTranslations[] = [
                                            'attribute_choice_id' => $attributeChoiceId,
                                            'locale' => $locale,
                                            'slug' => $machineName,
                                            'name' => $choiceTranslation,
                                        ];

                                        $existingAttributesChoicesTranslations[$uniqueKey] = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $chunkAttributesChoicesTranslationData = array_chunk($dataToInsertAttributesChoicesTranslations, 500);

            foreach ($chunkAttributesChoicesTranslationData as $chunkTranslationData) {
                $this->attributeChoiceService->insertTranslation($chunkTranslationData);
            }

        } catch (\Throwable $th) {
            Log::error('Exception caught', ['exception' => $th]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $this->info('Attributes choices import task has finished for: ' . $executionTime);
    }
}
