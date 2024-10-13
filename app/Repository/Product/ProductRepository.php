<?php

namespace App\Repository\Product;

use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ProductRepository implements EloquentRepositoryInterface
{

    public function create(array $attributes): Model
    {
        return Product::create($attributes);
    }

    public function createTranslation(array $attributes): Model
    {
      return ProductTranslation::create($attributes);
    }

    public function find($id): ?Model
    {
        return Product::find($id);
    }
}
