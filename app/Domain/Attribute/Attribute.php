<?php

namespace App\Domain\Attribute;

use Database\Factories\AttributeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected static function newFactory(): AttributeFactory
    {
        return new AttributeFactory();
    }

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
}
