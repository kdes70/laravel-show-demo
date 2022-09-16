@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Domain\Product\Product[] $products */
@endphp

<x-app-layout title="Products">
    <div class="flex mr-5">
        <div class="mx-auto">
            <form action="{{action(App\Http\Controllers\Products\FiltersController::class)}}" method="get">
                @csrf
                <div class="mt-4 flex items-center justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-500 font-display font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:ring focus:ring-red-700">
                        Filter
                    </button>
                </div>
                @foreach($attributes as $attribute)
                    <div class="mt-8">
                        <label class="block font-semibold text-sm text-gray-600" for="street">
                            {{$attribute->name}}
                        </label>
                        @if(!$attribute->is_multiple)
                            <select
                                class="px-2 font-s ans border-2 border-gray-300 focus:outline-none focus:border-red-400 focus:ring-0 p-2 w-full"
                                name="filters[{{$attribute->slug}}]"
                            >
                                <option value="" disabled selected>Выберите город</option>
                                @foreach($attribute->values as $value)
                                    <option
                                        value="{{$value->id}}"
                                        @if(isset(app('request')->get("filters")[$attribute->slug]) && app('request')->get("filters")[$attribute->slug] == $value->id)
                                            selected
                                        @endif
                                    >{{$value->name}}</option>
                                @endforeach
                            </select>
                        @else
                            @foreach($attribute->values as $value)
                                <div class="block">
                                    <div class="mt-2">
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox"
                                                       name="filters[{{$attribute->slug}}][{{$loop->index}}]"
                                                       @if(isset(app('request')->get("filters")[$attribute->slug][$loop->index]) && app('request')->get("filters")[$attribute->slug][$loop->index] == $value->id)
                                                           checked
                                                       @endif
                                                       value="{{$value->id}}"
                                                >
                                                <span class="ml-2">{{$value->name}}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </form>
        </div>
        <div class="mx-auto grid grid-cols-3 gap-12">

            @foreach($products as $product)
                <x-product
                    :title="$product->name"
                    :price="format_money($product->getItemPrice()->pricePerItemIncludingVat())"
                    :actionUrl="action(\App\Http\Controllers\Cart\AddCartItemController::class, [$product])"
                />
            @endforeach
        </div>
    </div>


    <div class="mx-auto mt-12">
        {{ $products->links() }}
    </div>
</x-app-layout>
