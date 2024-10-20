<?php

namespace App\Models\AttributeChoice;

use App\Models\Attribute\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeChoice extends Model
{
    use HasFactory;

    public $guarded = [];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
