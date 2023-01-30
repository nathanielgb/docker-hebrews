<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('item', {
                    update: [],
                    delete: [],
                    void: [],
                })
            })
        </script>
    </x-slot>

    <x-slot name="header">
        {{ __('Update Order Items') }}
    </x-slot>

    @include('components.alert-message')
    <div class="flex justify-between my-3">
        <div>
            <a
                href="{{ route('order.show_summary', $order->order_id) }}"
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

    <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs"
        x-data="{ updateOrderItemId: '', updateOrderItemName: '', updateOrderItemQty: '', deleteOrderItemId: '', deleteOrderItemName: '' }">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3 text-center">O.Type</th>
                    <th class="px-4 py-3">Qty</th>
                    <th class="px-4 py-4 text-center">Addons</th>
                    <th class="px-4 py-3 text-center">Price</th>
                    <th class="px-4 py-3 text-center">Total Amount</th>
                    <th class="px-4 py-3">Note</th>
                    {{-- <th class="px-4 py-3 text-center">Served by</th> --}}
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($order_items as $item)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm">
                                {{ $item->name }}
                                @if (isset($item->data['grind_type']) && !empty($item->data['grind_type']))
                                    ({{ $item->data['grind_type'] }})
                                @endif
                            </td>
                            <td class="px-3 py-3 text-sm text-center">
                                @if (isset($item->data['is_dinein']) && $item->data['is_dinein'])
                                    <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Dine-in</span>
                                @else
                                    <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Take-out</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <ul>
                                    <li>
                                        <em>
                                            <span class="font-semibold">
                                                menu: {{ $item->menu_id }} <br>
                                            </span>
                                        </em>
                                    </li>
                                    <li>
                                        @if ($item->inventory_code)
                                            <em >
                                                inventory:
                                                <span class="font-bold">
                                                    {{ $item->inventory_code }}
                                                </span>
                                            </em>
                                        @endif
                                    </li>
                                    <li>qty: <span class="font-semibold"><em>{{ $item->qty }}</em></span></li>
                                    <li>unit/qty:
                                        <em>
                                            <span class="font-semibold">
                                                {{ $item->units }}
                                                @if ($item->unit_label)
                                                    ({{ $item->unit_label }})
                                                @endif
                                            </span>
                                        </em>
                                    </li>
                                    <li>tot.qty: <span class="font-semibold"><em>{{ $item->qty * $item->units  }}</em></span></li>
                                </ul>
                            </td>
                            <td class="px-4 py-4 text-sm text-center">
                                @if ($order->confirmed)
                                    @if (isset($item->data['has_addons']) && $item->data['has_addons'])
                                        @php
                                            $addons = json_encode($item->addons);
                                        @endphp
                                        <button
                                            class="btn-addons inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-green-600 rounded-full leading-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#addOnItemsModal"
                                            data-addons="{{ $addons }}"

                                            >
                                            <i class="fa-solid fa-eye"></i>&nbsp;YES
                                        </button>
                                    @else
                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                            NO
                                        </div>
                                @endif
                                @else
                                    @if (isset($item->data['has_addons']) && $item->data['has_addons'])
                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-green-600 rounded-full leading-sm">
                                            YES
                                        </div>
                                    @else
                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                            NO
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $item->price }} ({{ $item->type }})
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $item->total_amount }}
                            </td>
                            <td class="px-4 py-3 text-sm text-justify" style="max-width: 150px;">
                                <p>
                                    {{ $item->note }}
                                </p>
                            </td>
                            {{-- <td class="px-4 py-3 text-sm text-center">
                                <p>
                                    {{ $item->served_by }}
                                </p>
                            </td> --}}
                            <td class="px-4 py-4 text-sm" style="max-width: 100px;">
                                @if ($item->status == 'pending')
                                    @if (isset($item->errors) && count($item->errors) > 0)
                                        @foreach($item->errors as $error)
                                            <li class="text-red-600">{{ $error }}</li>
                                        @endforeach
                                    @else
                                        <div class="text-center">
                                            <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-yellow-400 rounded-full leading-sm">
                                                PENDING
                                            </div>
                                        </div>
                                    @endif
                                @elseif ($item->status == 'ordered')
                                    <div class="text-center">
                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-blue-700 uppercase bg-blue-200 rounded-full leading-sm">
                                            ORDERED
                                        </div>
                                    </div>
                                @elseif ($item->status == 'preparing')
                                    <div class="text-center">
                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-orange-700 uppercase bg-orange-200 rounded-full leading-sm">
                                            PREPARING
                                        </div>
                                    </div>
                                @elseif ($item->status == 'done')
                                    <div class="text-center">
                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-green-700 uppercase bg-green-200 rounded-full leading-sm">
                                            DONE
                                        </div>
                                    </div>
                                @elseif ($item->status == 'served')
                                    <div class="text-center">
                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-teal-700 uppercase bg-teal-200 rounded-full leading-sm">
                                            SERVED
                                        </div>
                                    </div>
                                @elseif ($item->status == 'void')
                                    <div class="text-center">
                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                            VOID
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <div class="flex flex-col items-center justify-center space-y-4 text-sm">
                                    @if (!$order->confirmed)
                                        @if ($item->status != 'served')
                                            <button
                                                class="btn-update-order flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editItemModal"
                                                data-orderitem="{{ json_encode($item) }}"
                                                @click="$store.item.update={{ json_encode([
                                                    'id' => $item->id,
                                                    'name' => $item->name,
                                                    'qty' => $item->qty,
                                                ]) }}"
                                                >
                                                <span><i class="fa-solid fa-pen"></i> Update</span>
                                            </button>

                                            <button
                                                class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"                                            aria-label="Delete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteItemModal"
                                                @click="$store.item.delete={{ json_encode([
                                                    'id' => $item->id,
                                                    'name' => $item->name,
                                                ]) }}"
                                                >
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </button>
                                        @endif
                                    @elseif (!$order->confirmed || !$order->complete || !$order->cancelled)
                                        @if ($item->status != 'void')
                                            <button
                                                class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"                                            aria-label="Delete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#voidItemModal"
                                                @click="$store.item.void={{ json_encode([
                                                    'id' => $item->id,
                                                    'name' => $item->name,
                                                ]) }}"
                                                >
                                                <i class="fa-solid fa-ban"></i> Void
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="text-gray-700">
                            <td colspan="11" class="px-4 py-3 text-sm text-center">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @include('orders.modals.edit_item')
    @include('orders.modals.delete_item')
    @include('orders.modals.void_item')
    @include('orders.modals.order_inventory_summary')
    @include('orders.modals.show_addons')

    <x-slot name="scripts">
        <script>
            $('.btn-update-order').on("click", function() {
                var orderItem = JSON.stringify($(this).data('orderitem'));
                Livewire.emit('setItem', orderItem);

            });
            $('.btn-addons').on("click", function() {
                var addons = JSON.stringify($(this).data('addons'));

                Livewire.emit('setAddOnItem', addons);
            });
        </script>
    </x-slot>

</x-app-layout>
