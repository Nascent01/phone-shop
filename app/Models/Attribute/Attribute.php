<?php

namespace App\Models\Attribute;

use App\Models\AttributeChoice\AttributeChoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    public $timestamps = true;

    public $fillable = ['created_at, updated_at'];

    public function choices()
    {
        return $this->hasMany(AttributeChoice::class);
    }
}
