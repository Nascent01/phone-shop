<?php

namespace App\Repository\ProductCategory;

use App\Models\ProductCategory\ProductCategory;
use App\Models\ProductCategory\ProductCategoryTranslation;
use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ProductCategoryRepository implements EloquentRepositoryInterface {

    /**
     * Create a new product category.
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model {
        return ProductCategory::create($attributes);
    }

    public function createTranslation(array $attributes): Model {
        return ProductCategoryTranslation::create($attributes);
    }

    /**
     * Find a product category by ID.
     *
     * @param $id
     * @return Model|null
     */
    public function find($id): ?Model {
        return ProductCategory::find($id);
    }
}
