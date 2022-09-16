<?php

namespace App\Domain\Product\Search;

use Illuminate\Pagination\LengthAwarePaginator;

interface SearchContract
{
    public function facetedSearch(array $parameters): LengthAwarePaginator;
}
