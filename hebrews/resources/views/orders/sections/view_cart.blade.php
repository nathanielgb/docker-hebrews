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
        <div>
            <button
                type="button"
                class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                data-bs-toggle="modal"
                data-bs-target="#inventorySummaryModal"
                >
                <i class="fa-solid fa-cubes"></i> INVENTORIES USED
            </button>
        </div>
    </div>
    <div class="p-6 overflow-hidden bg-white rounded-lg shadow-xs">
        <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-4 text-center">M.ID</th>
                        <th class="px-4 py-4">Name</th>
                        <th class="px-4 py-4 text-center">O.Type</th>
                        <th class="px-4 py-4">Inventory</th>
                        <th class="px-4 py-3">Qty</th>
                        <th class="px-4 py-4 text-center">Addons</th>
                        <th class="px-4 py-4 text-center">Price</th>
                        <th class="px-4 py-3 text-center">Total</th>
                        <th class="px-4 py-4 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @forelse ($cart_items as $item)
                            <tr>
                                <td class="px-4 py-4 text-sm text-center">
                                    {{ $item->menu_id }}
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    {{ $item->menu->name ?? 'N/A' }}
                                    @if (isset($item->data['grind_type']) && !empty($item->data['grind_type']))
                                        ({{ $item->data['grind_type'] }})
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-center">
                                    @if (isset($item->data['is_dinein']) && $item->data['is_dinein'])
                                        <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Dine-in</span>
                                    @else
                                        <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Take-out</span>
                                    @endif

                                </td>
                                <td class="px-4 py-4 text-sm">
                                    @if (isset($item->menu->inventory))
                                        <ul>
                                            <li>Branch: <span class="font-bold">{{ $item->menu->inventory->branch->name }}</span></li>
                                            <li>Name: <span class="font-bold">{{ $item->menu->inventory->name }}</span></li>
                                            <li>Code: <span class="font-bold">{{ $item->menu->inventory->inventory_code }}</span></li>
                                            <li>Stock: <span class="font-bold">{{ $item->menu->inventory->stock }}</span></li>
                                            <li>Unit: <span class="font-bold">{{ $item->menu->inventory->unit }}</span></li>
                                        </ul>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <ul>
                                        <li>Qty: <span class="font-bold">{{ $item->qty }}</span></li>
                                        <li>Unit/Qty: <span class="font-bold">{{ $item->menu->units }}</span></li>
                                        <li>Tot.Qty: <span class="font-bold">{{ $item->qty * $item->menu->units  }}</span></li>
                                    </ul>
                                </td>
                                <td class="px-4 py-4 text-sm text-center">
                                    @if (isset($item->data['has_addons']) && $item->data['has_addons'])
                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-green-600 rounded-full leading-sm">
                                            YES
                                        </div>
                                    @else
                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                            NO
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-center">
                                    {{ $item->price }} ({{ $item->type ?? 'N/A' }})
                                </td>
                                <td class="px-4 py-4 text-sm text-center">
                                    {{ $item->total }}
                                </td>
                                <td class="px-4 py-4 text-sm" style="max-width: 150px;">
                                    @if ($item->available)
                                        @if (isset($item->errors) && count($item->errors) > 0)
                                            @foreach($item->errors as $error)
                                                <li class="text-red-600">{{ $error }}</li>
                                            @endforeach
                                        @else
                                            <div class="text-center">
                                                <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-green-600 rounded-full leading-sm">
                                                    AVAILABLE
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center">
                                            <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                                UNAVAILABLE
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <div class="flex flex-col items-center justify-center space-y-4 text-sm">
                                        @if ($item->available)
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
                                        @endif
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

    @include('orders.modals.confirm_cart')
    @include('orders.modals.inventory_summary')
    @include('orders.modals.update_cart_item')
    @include('orders.modals.delete_cart_item')

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script type="text/javascript">
            $('.custom-discount').hide();
            $('#customer-name').hide();
            $('#customer-account').show();

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

            $('#noAccountBox').on("click", function() {
                var noAccount = $(this).is(':checked');
                if (noAccount) {
                    $('#customer-name').show();
                    $('#customer-account').hide();
                } else {
                    $('#customer-name').hide();
                    $('#customer-account').show();
                }
            });

        </script>
    </x-slot>


</x-app-layout>
