<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

interface BaseServiceInterface
{
    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * @param array $attributes
     * @return Model
     */
    public function createTranslation(array $attributes): Model;
}
