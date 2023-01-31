<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('cart', {
                    addInventoryData: [],
                })
            })
        </script>

    </x-slot>

    <div class="container grid px-6 mx-auto space-y-2">
        <x-slot name="header">
            {{ __('Add to Cart') }}
        </x-slot>
        @include('components.alert-message')
        <div>
            <div class="p-4 bg-white rounded-lg shadow-xs">
                <div class="flex justify-end my-3">
                    <div class="flex space-x-2 jusify-center">
                        <a
                            href="{{ route('order.show_add_cart') }}"
                            class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                            >
                            <span><i class="fa-solid fa-list"></i> VIEW ALL</span>
                        </a>
                        <button
                            type="button"
                            class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                            data-bs-toggle="modal"
                            data-bs-target="#searchAddCartModal"
                            >
                            <i class="fa-solid fa-magnifying-glass"></i> SEARCH
                        </button>
                        <a
                            href="{{ route('order.show_cart') }}"
                            class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                            >
                            <span><i class="fa-solid fa-cart-shopping"></i> VIEW CART</span>
                        </a>
                    </div>

                </div>

                <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
                    <div class="w-full overflow-x-auto">
                        <table class="w-full whitespace-no-wrap">
                            <thead>
                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3 text-center">No. of Units</th>
                                <th class="px-4 py-3">Inventory</th>
                                <th class="px-4 py-3 text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y">
                                @forelse ($menu as $item)
                                    <tr class="text-gray-700">
                                        <td class="px-4 py-3 text-sm">
                                            {{ $item->name }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span>
                                                {{ $item->category->name }}
                                            </span> <br>
                                            <span class="italic">
                                                {{ $item->sub_category }}
                                            </span><br>
                                            <span class="italic">
                                                ({{ $item->category->from }})
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            {{ $item->units }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if (isset($item->inventory))
                                                <ul>
                                                    <li>Branch: <span class="font-bold">{{ $item->inventory->branch->name }}</span></li>
                                                    <li>Name: <span class="font-bold">{{ $item->inventory->name }}</span></li>
                                                    <li>Code: <span class="font-bold">{{ $item->inventory->inventory_code }}</span></li>
                                                    <li>Stock: <span class="font-bold">{{ $item->inventory->stock }}</span></li>
                                                    <li>Unit: <span class="font-bold">{{ $item->inventory->unit }}</span></li>
                                                </ul>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center text-sm">
                                                <button
                                                    class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out btn-add-cart"
                                                    type="button"
                                                    data-cart="{{ json_encode($item) }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#addCartModal"
                                                    @click="$store.cart.addCartData={{ json_encode($item) }}"
                                                    >
                                                    <span><i class="fa-solid fa-plus-circle"></i> Add</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="text-gray-700">
                                        <td colspan="8" class="px-4 py-3 text-sm text-center">
                                            No records found.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if ($menu->hasPages())
                <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                    {{ $menu->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
    @include('orders.modals.add_cart')
    @include('orders.modals.search_add_cart')

    <x-slot name="scripts">
        <script>
            $('.btn-add-cart').on("click", function() {
                var cart = JSON.stringify($(this).data('cart'));
                Livewire.emit('setCartItem', cart);
            });

            $('#add-cart-form').on("submit", function() {
                $('.cart-submit').prop('disabled', true);
            });

            // $('.addon-active').on("click", function() {
            //     var state = $(this).is(":checked");
            //     $( ".menu-addons" ).toggle(state);
            // });
        </script>
    </x-slot>
</x-app-layout>
