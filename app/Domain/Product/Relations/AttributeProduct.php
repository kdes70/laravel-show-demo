<?php

namespace App\Domain\Product\Relations;

use App\Domain\Product\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AttributeProduct extends Pivot
{
    protected $table = 'attribute_products';

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

}
