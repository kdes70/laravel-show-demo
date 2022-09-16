<?php

namespace Tests\Feature;

use App\Domain\Attribute\Attribute;
use App\Domain\Attribute\AttributeValue;
use App\Domain\Product\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFilteredTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_route_is_work()
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

        $queryData['filters'][$attributeSimple->slug] = $value;

        $url = extend_url_with_query_data('product/filters', $queryData);

        $this->get($url)
            ->assertOk();
    }

    public function test_filter_is_empty()
    {
        $this->get('product/filters')
            ->assertStatus(302)
            ->assertRedirect('/');
    }

    public function test_filter_is_not_multiple_should_allow_select_only_one_filter_value_products()
    {
        $attributeSimple = Attribute::factory([
            'is_multiple' => false
        ])->has(AttributeValue::factory()->count(3), 'values')
            ->create();

        $values = collect($attributeSimple->values->modelKeys())->random(2);

        $queryData['filters'][$attributeSimple->slug] = $values->toArray();

        $this->get(extend_url_with_query_data('product/filters', $queryData))
            ->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors(["filters.{$attributeSimple->slug}"]);

    }

    public function test_can_select_multiple_filter_the_same_time()
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

        $queryData['filters'][$attributeMulti->slug] = $valuesMulti->toArray();
        $queryData['filters'][$attributeSimple->slug] = $valuesSimple;


        $this->get(extend_url_with_query_data('product/filters', $queryData))
            ->assertOk();
    }
}
