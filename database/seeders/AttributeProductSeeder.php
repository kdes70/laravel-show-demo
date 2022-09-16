<?php

namespace Database\Seeders;

use App\Domain\Attribute\Attribute;
use App\Domain\Attribute\AttributeValue;
use App\Domain\Product\Product;
use App\Domain\Product\Relations\AttributeProduct;
use Illuminate\Database\Eloquent\Model;
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
        $attributeCity = $this->createAttribute(
            'Город',
            'city',
            self::CiTIES_COUNT,
            true
        );

        $attributeCategory = $this->createAttribute(
            'Категория',
            'category',
            self::CATEGORIES_COUNT,
            false,
            1
        );

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

        $data = [];

        for ($i = 0; $i < self::COUNT_PRODUCT_ITEMS; $i++) {
            $data[] = Product::factory()->make()->toArray();
        }

        $chunks = array_chunk($data, self::CHUNK_COUNT);

        foreach ($chunks as $chunk) {
            Product::insert($chunk);
            $this->command->getOutput()->progressAdvance(count($chunk));
        }

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

    private function createAttribute(string $name, string $slug, int $valueCount, bool $isMultiple = false, $sort = 0): Model|Collection
    {
        return Attribute::factory([
            'name' => $name,
            'slug' => $slug,
            'is_multiple' => $isMultiple,
            'sort' => $sort
        ])->has(
            AttributeValue::factory()->count($valueCount), 'values'
        )->create();
    }
}
