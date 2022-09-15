<?php

namespace Database\Factories;

use App\Domain\Attribute\Attribute;
use App\Domain\Attribute\AttributeValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AttributeValueFactory extends Factory
{
    protected $model = AttributeValue::class;

    public function definition(): array
    {
        return [
            'attribute_id' => Attribute::factory(),
            'name'         => $title = 'Value ' . strtoupper($this->faker->bothify('?????-##')),
            'slug'         => Str::slug($title),
        ];
    }
}
