<?php

namespace App\Domain\Product\Relations;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AttributeProduct extends Pivot
{
    protected $table = 'attribute_products';
}
