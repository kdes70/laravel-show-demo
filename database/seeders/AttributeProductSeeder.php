<?php

namespace Database\Seeders;

use App\Domain\Attribute\Attribute;
use App\Domain\Attribute\AttributeValue;
use App\Domain\Product\Product;
use App\Domain\Product\Relations\AttributeProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class AttributeProductSeeder extends Seeder
{
    const COUNT_PRODUCT_ITEMS = 50000;
    const CHUNK_COUNT = 5000;
    const CATEGORIES_COUNT = 100;
    const CiTIES_COUNT = 30;

    /**
     * @throws \Throwable
     */
    public function run()
    {
        $attributeCategory = Attribute::factory([
            'name' => 'Категория',
            'slug' => 'category',
            'is_multiple' => false
        ])->has(
            AttributeValue::factory()->count(self::CATEGORIES_COUNT), 'values'
        )->create();

        $attributeCity = Attribute::factory([
            'name' => 'Город',
            'slug' => 'city',
            'is_multiple' => true
        ])->has(
            AttributeValue::factory()->count(self::CiTIES_COUNT), 'values'
        )->create();

        \DB::transaction(function () use ($attributeCity, $attributeCategory) {
            $this->productRecords();

            $citiesKey = collect($attributeCity->values->modelKeys());
            $this->attributeAssigned($citiesKey, 'Города');

            $categoriesKey = collect($attributeCategory->values->modelKeys());
            $this->attributeAssigned($categoriesKey, 'Категории');
        });
    }

    private function productRecords(): void
    {
        $this->command->info("Товары");
        $this->command->getOutput()->progressStart(self::COUNT_PRODUCT_ITEMS);

        $data = collect();

        for ($i = 0; $i < self::COUNT_PRODUCT_ITEMS; $i++) {
            $data->push(Product::factory([])->make()->toArray());
        }

        $data->chunk(self::CHUNK_COUNT)->each(function (Collection $chunkProducts) {
            Product::insert($chunkProducts->toArray());

            $this->command->getOutput()->progressAdvance($chunkProducts->count());
        });

        $this->command->getOutput()->progressFinish();
    }

    /**
     * @throws \Throwable
     */
    private function attributeAssigned(Collection $attributeKeys, string $attributeName): void
    {
        $uniqueItems = $this->getAttributeData($attributeKeys, $attributeName);;

        $this->command->info("$attributeName - Линкуем данные...");
        $this->command->getOutput()->progressStart($uniqueItems->count());

        $uniqueItems->chunk(self::CHUNK_COUNT)->each(function (Collection $chunks) {
            \DB::transaction(function () use ($chunks) {

                AttributeProduct::insert($chunks->toArray());

                $this->command->getOutput()->progressAdvance($chunks->count());
            });
        });

        $this->command->getOutput()->progressFinish();
    }

    private function getAttributeData(Collection $attributeKeys, string $attributeName): Collection
    {
        $this->command->info("$attributeName - Формируем данные...");
        $attributeItems = collect();

        foreach (Product::cursor() as $product) {
            $item = $this->getItem($product->id, $attributeItems, $attributeKeys);
            $attributeItems->push($item);
        }

        return $attributeItems;
    }

    private function getItem(int $productId, Collection &$attributeItems, Collection $attributeKeys): array
    {
        do {
            $item = [
                'product_id' => $productId,
                'value_id' => $attributeKeys->random()
            ];

        } while ($attributeItems->contains($item));

        return $item;
    }
}
