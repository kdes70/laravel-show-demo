<?php

namespace App\Domain\Attribute;

use App\Domain\Product\Product;
use App\Domain\Product\Relations\AttributeProduct;
use Database\Factories\AttributeValueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttributeValue extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['parentAttribute'];

    protected $touches = ['products'];

    public $timestamps = false;

    protected static function newFactory(): AttributeValueFactory
    {
        return new AttributeValueFactory();
    }

    public function parentAttribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'attribute_products',
            'product_id',
            'value_id'
        )->using(AttributeProduct::class);
    }
}
