<?php

namespace Database\Factories;

use App\Domain\Attribute\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition(): array
    {
        return [
            'name' => $title = 'Name ' . strtoupper($this->faker->bothify('?????-##')),
            'slug' => Str::slug($title),
            'description' => $this->faker->sentence,
            'is_multiple' => $this->faker->boolean,
            'sort' => 0
        ];
    }
}
