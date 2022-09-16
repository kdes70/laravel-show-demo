<?php

namespace App\Domain\Product\Search;

use App\Domain\Attribute\Attribute;
use App\Domain\Product\Product;
use App\Domain\Product\Relations\AttributeProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentSearch implements SearchContract
{
    public function facetedSearch(array $parameters): LengthAwarePaginator
    {
        $filtersValue = $this->getFlattenParametersValue($parameters);

        $result = AttributeProduct::where(
            function (Builder $query) use ($filtersValue) {
                $query->whereIn('value_id', $filtersValue);
            })->paginate();

        // process raw results
        $productIds = array_map(function ($index) {
            return $index->product_id;
        }, $result->items());

        // return paginated results
        return new LengthAwarePaginator(
            Product::findMany($productIds),
            $result->total(),
            $result->perPage()
        );
    }

    private function getFlattenParametersValue(array $parameters): Collection
    {
        $parametersCollect = collect($parameters);

        $attributes = Attribute::whereIn('slug', $parametersCollect->keys())->get();

        return $attributes->map(function (Attribute $attribute) use ($parametersCollect) {
            //если is_multiple false то выбираем только по одному значению
            if (!$attribute->is_multiple) {
                return is_array($parametersCollect->get($attribute->slug)) ? $parametersCollect->get($attribute->slug)[0] : $parametersCollect->get($attribute->slug);
            }

            return $parametersCollect->get($attribute->slug);
        })->flatten();
    }
}
