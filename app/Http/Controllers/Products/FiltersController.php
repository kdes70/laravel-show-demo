<?php

namespace App\Http\Controllers\Products;

use App\Domain\Attribute\Attribute;
use App\Domain\Product\Search\SearchContract;
use App\Http\Requests\FilterRequest;
use App\Providers\RouteServiceProvider;

class FiltersController
{
    public function __invoke(FilterRequest $request, SearchContract $search)
    {
        if (!$request->filters) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $products = $search->facetedSearch($request->filters);

        $attributes = Attribute::with('values')->orderByDesc('sort')->get();

        return view('products.index', compact(['attributes', 'products']));
    }
}
