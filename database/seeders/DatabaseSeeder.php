<?php

namespace Database\Seeders;

use App\Domain\Attribute\Attribute;
use App\Domain\Attribute\AttributeValue;
use App\Domain\Cart\Actions\AddCartItem;
use App\Domain\Cart\Actions\InitializeCart;
use App\Domain\Coupon\Coupon;
use App\Domain\Customer\Customer;
use App\Domain\Product\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Ramsey\Collection\Collection;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::factory(100)->create();

        Coupon::factory()->create();

        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'email' => 'admin@shop.com',
            'name' => 'Admin',
        ]);

        $customer = Customer::create([
            'name' => $user->name,
            'email' => $user->email,
            'user_id' => $user->id,
            'street' => 'Street',
            'number' => '101',
            'postal' => '2000',
            'city' => 'City',
            'country' => 'Belgium',
        ]);

        $cart = (new InitializeCart)($customer);

        (new AddCartItem)($cart, $products->random(1)[0], 1);
        (new AddCartItem)($cart, $products->random(1)[0], 1);
        (new AddCartItem)($cart, $products->random(1)[0], 1);


//        $this->call([
//            AttributeProductSeeder::class
//        ]);
    }
}
