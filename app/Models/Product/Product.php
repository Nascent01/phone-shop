<?php

namespace App\Models\Product;

use App\Models\ProductCategory\ProductCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['sku'];

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_product_category');
    }
}
