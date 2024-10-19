<?php

namespace App\Repository\Product;

use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ProductRepository implements EloquentRepositoryInterface
{
    /**
     * Find a product by ID.
     *
     * @param $id
     * @return Model|null
     */
    public function find($id): ?Model
    {
        return Product::find($id);
    }
}
