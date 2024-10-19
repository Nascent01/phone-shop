<?php

namespace App\Repository\ProductCategory;

use App\Models\ProductCategory\ProductCategory;
use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ProductCategoryRepository implements EloquentRepositoryInterface {

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
