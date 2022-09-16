<?php

namespace App\Http\Controllers\Products;

use App\Domain\Attribute\Attribute;
use App\Domain\Product\Product;
use App\Http\Requests\FilterRequest;

class ProductIndexController
{
    public function __invoke(FilterRequest $request)
    {
        $attributes = Attribute::with('values')->orderByDesc('sort')->get();

        $products = Product::paginate();

        return view('products.index', compact(['attributes', 'products']));
    }
}
