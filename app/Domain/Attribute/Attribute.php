<?php

namespace App\Domain\Attribute;

use Database\Factories\AttributeFactory;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin EloquentBuilder
 * @mixin QueryBuilder
 */
class Attribute extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'is_multiple' => 'boolean'
    ];

    protected static function newFactory(): AttributeFactory
    {
        return new AttributeFactory();
    }

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
}
