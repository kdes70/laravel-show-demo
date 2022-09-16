<?php

namespace Tests\Domain\Product;

use App\Domain\Attribute\Attribute;
use App\Domain\Attribute\AttributeValue;
use App\Domain\Product\Product;
use App\Domain\Product\Search\SearchContract;
use Tests\TestCase;

class ProductFilteredTest extends TestCase
{
    public function test_is_can_filter_product_work()
    {
        $attributeSimple = Attribute::factory([
            'is_multiple' => false
        ])->has(
            AttributeValue::factory()->count(3), 'values'
        )->create();

        $products = Product::factory()->count(10)->create();

        $products->each(function (Product $product) use ($attributeSimple) {
            $product->attributes()->sync($attributeSimple->values->modelKeys());
        });

        $value = collect($attributeSimple->values->modelKeys())->random();

        $queryData[$attributeSimple->slug] = $value;

        $result = app(SearchContract::class)->facetedSearch($queryData);

        $this->assertEquals(10, $result->count());
    }

    public function test_is_can_select_only_one_filter_product_work()
    {
        $attributeSimple = Attribute::factory([
            'is_multiple' => false
        ])->has(
            AttributeValue::factory()->count(2), 'values'
        )->create();

        $productsOne = Product::factory()->count(3)->create();
        $productsTwo = Product::factory()->count(3)->create();

        $valueOne = collect($attributeSimple->values->modelKeys())->get(0);
        $valueTwo = collect($attributeSimple->values->modelKeys())->get(1);

        $productsOne->each(function (Product $product) use ($valueOne) {
            $product->attributes()->sync([$valueOne]);
        });

        $productsTwo->each(function (Product $product) use ($valueTwo) {
            $product->attributes()->sync([$valueTwo]);
        });

        $queryData[$attributeSimple->slug][] = $valueOne;
        $queryData[$attributeSimple->slug][] = $valueTwo;

        $result = app(SearchContract::class)->facetedSearch($queryData);

        $this->assertEquals(3, $result->count());
    }

    public function test_can_have_multiple_filters_same_time()
    {
        $attributeMulti = Attribute::factory([
            'is_multiple' => true
        ])->has(AttributeValue::factory()->count(3), 'values')
            ->create();

        $attributeSimple = Attribute::factory([
            'is_multiple' => false
        ])->has(AttributeValue::factory()->count(3), 'values')
            ->create();

        $valuesMulti = collect($attributeMulti->values->modelKeys())->random(2);
        $valuesSimple = collect($attributeSimple->values->modelKeys())->random();

        $productsOne = Product::factory()->count(3)->create();
        $productsTwo = Product::factory()->count(3)->create();

        $productsOne->each(function (Product $product) use ($valuesMulti) {
            $product->attributes()->sync($valuesMulti);
        });

        $productsTwo->each(function (Product $product) use ($valuesSimple) {
            $product->attributes()->sync([$valuesSimple]);
        });

        $queryData[$attributeMulti->slug] = $valuesMulti->toArray();
        $queryData[$attributeSimple->slug] = $valuesSimple;

        $result = app(SearchContract::class)->facetedSearch($queryData);
        $this->assertEquals(6, $result->count());
    }
}
