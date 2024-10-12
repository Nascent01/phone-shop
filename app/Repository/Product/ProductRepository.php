<?php

namespace App\Repository\Product;

use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ProductRepository implements EloquentRepositoryInterface
{

    public function create(array $attributes): Model
    {
        // TODO: Implement create() method.
    }

    public function createTranslation(array $attributes): Model
    {
        // TODO: Implement createTranslation() method.
    }

    public function find($id): ?Model
    {
        // TODO: Implement find() method.
    }
}
