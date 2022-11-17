<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('cart', {
                    deleteData: [],
                    updateData: [],
                })
            })
        </script>
    </x-slot>

    <x-slot name="styles">
        <link
            href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css"
            rel="stylesheet"
        />
    </x-slot>

    <x-slot name="header">
        {{ __('Cart Items') }}
    </x-slot>

    @include('components.alert-message')

    <div class="flex justify-between my-3">
        <div>
            <a
                href="{{ route('order.show_add_cart') }}"
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                >
                <span><i class="fa-solid fa-circle-arrow-left"></i> BACK</span>
            </a>
        </div>
    </div>
    <div class="p-4 overflow-hidden bg-white rounded-lg shadow-xs">
        <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-4">Branch</th>
                        <th class="px-4 py-4 text-center">Menu ID</th>
                        <th class="px-4 py-4">Name</th>
                        <th class="px-4 py-4 text-center">Status</th>
                        <th class="px-4 py-4 text-center">Type</th>
                        <th class="px-4 py-4 text-center">Units/Qty</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-4 text-center">Price</th>
                        <th class="px-4 py-3 text-center">Total</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @forelse ($cart_items as $item)
                            <tr>
                                <td class="px-4 py-4 text-sm">
                                    {{ $item->inventory->branch->name }}
                                </td>
                                <td class="px-4 py-4 text-sm text-center">
                                    {{ $item->menu_id }}
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    {{ $item->name }}
                                </td>
                                <td class="px-4 py-4 text-sm text-center">
                                    @if ($item->available)
                                        <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-green-500 text-white rounded-full">Available</span>
                                    @else
                                        <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-red-600 text-white rounded-full">Unavailable</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-center">
                                    {{ $item->type }}
                                </td>
                                <td class="px-4 py-4 text-sm text-center">
                                    {{ $item->units }} ({{ $item->inventory->unit }})
                                </td>
                                <td class="px-4 py-4 text-sm text-center">
                                    {{ $item->qty }}
                                </td>

                                <td class="px-4 py-4 text-sm text-center">
                                    {{ $item->price }}
                                </td>
                                <td class="px-4 py-4 text-sm text-center">
                                    {{ $item->total }}
                                </td>
                                {{-- <td class="px-4 py-4 text-sm">
                                    @if ($item->data)
                                        <ul>
                                            @foreach ($item->data as $addon)
                                                <li>{{ $addon['name'] }} - {{ $addon['qty'] }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td> --}}
                                <td class="px-4 py-4 text-sm f">
                                    <div class="flex items-center justify-center space-x-4 text-sm">
                                        <button
                                            class="btn-update-cart flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                            type="button"
                                            data-cart="{{ json_encode($item) }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#updateCartModal"
                                            @click="$store.cart.updateData={{ json_encode($item) }}"
                                            >
                                            <span><i class="fa-solid fa-pen"></i> Update</span>
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                                            aria-label="Delete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteCartModal"
                                            @click="$store.cart.deleteData={{ json_encode([
                                                'id' => $item->id,
                                            ]) }}"
                                            >
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="text-gray-700">
                                <td colspan="10" class="px-4 py-3 text-sm text-center">
                                    No items found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex justify-center">
            @if ($unavailable_items > 0 || count($cart_items) <= 0)
                <button type="button" class="inline-block px-10 py-5 font-medium leading-tight text-white uppercase transition duration-150 ease-in-out bg-green-600 rounded shadow-md pointer-events-none text-s focus:outline-none focus:ring-0 opacity-60" disabled>ORDER</button>
            @else
                <button id="confirm-order" type="button" class="inline-block px-10 py-5 font-medium leading-tight text-white uppercase transition duration-150 ease-in-out bg-green-800 rounded shadow-lg text-s hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg" data-bs-toggle="modal" data-bs-target="#confirmCartModal">ORDER</button>
            @endif
        </div>
    </div>

    {{-- @include('orders.modals.confirm') --}}
    @include('orders.modals.confirm_cart')
    @include('orders.modals.update_cart_item')
    @include('orders.modals.delete_cart_item')

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script type="text/javascript">
            $('.custom-discount').hide();

            var select = new TomSelect('#select-table', {
                plugins: ['remove_button'],
            });

            select.addOption({value:'test'});

            $("#confirm-order, #order-discounts").click(function(){
                calculateOrderPrices();
            });

            $('.btn-update-cart').on("click", function() {
                var cart = JSON.stringify($(this).data('cart'));
                Livewire.emit('setCartItem', cart);

            });

            $('#confirm-cart-form').on("submit", function() {
                $('.cart-submit').prop('disabled', true);
            });

            // Toggle custom discount
            $('#order-discounts').on("change", function() {
                if ($(this).val() == 'custom') {
                    $('.custom-discount').show();
                } else {
                    $('.custom-discount').hide();
                }
            });

            $('#custom-discount-input').on("input", function() {
                calculateOrderPrices();
            });

            $('#fees-input').on("input", function() {
                calculateOrderPrices();
            });

            function calculateOrderPrices(data) {

                var subtotal = parseFloat({{ $cart_subtotal }});
                var discount_amt = 0;
                var display_discount_amt = 0.00;
                var total = 0;

                // Discount
                var discount = $('#order-discounts').find(":selected").data("discount");

                if (discount == 'custom') {
                    var discount_amt = parseFloat($('#custom-discount-input').val());

                    if (Number.isNaN(discount_amt) || discount_amt.length == 0) discount_amt = 0;

                    discount = {
                        type : "custom",
                        amount : discount_amt.toFixed(2)
                    }
                }

                var fees = parseFloat($('#fees-input').val());

                if (Number.isNaN(fees) || fees.length == 0  || fees < 0) fees = 0;
                if (Number.isNaN(subtotal) || subtotal.length == 0) subtotal = 0;

                // Parse discount
                if (discount?.type == 'percentage') {
                    let percentage = discount.amount / 100;
                    discount_amt = percentage * (subtotal+fees);
                    display_discount_amt = discount_amt.toFixed(2) + ' (' + discount.amount + '%)';
                } else if (discount?.type == 'flat') {
                    discount_amt = discount.amount;
                    display_discount_amt = discount_amt;
                } else if (discount?.type == 'custom') {
                    discount_amt = discount.amount;
                    display_discount_amt = discount_amt;
                }

                total = (subtotal + fees) - discount_amt;
                display_discount_amt = parseFloat(display_discount_amt);
                // Set values
                $('#ord-subtotal').val(subtotal.toFixed(2));
                $('#ord-discount').val(-display_discount_amt.toFixed(2));
                $('#ord-fees').val(fees.toFixed(2));
                $('#ord-total').val(total.toFixed(2));
            }
        </script>
    </x-slot>


</x-app-layout>
