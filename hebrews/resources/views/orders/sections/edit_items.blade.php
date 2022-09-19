<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('item', {
                    update: [],
                    delete: [],
                })
            })
        </script>
    </x-slot>

    <x-slot name="header">
        {{ __('Update Order Items') }}
    </x-slot>

    @include('components.alert-message')
    <div class="flex justify-start my-3">
        <div>
            <a
                href="{{ route('order.show_summary', $order_id) }}"
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                >
                <span><i class="fa-solid fa-circle-arrow-left"></i> BACK</span>
            </a>
        </div>
    </div>

    <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs"
        x-data="{ updateOrderItemId: '', updateOrderItemName: '', updateOrderItemQty: '', deleteOrderItemId: '', deleteOrderItemName: '' }">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3 text-center">Type</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Price</th>
                    <th class="px-4 py-3 text-center">Unit/Qty</th>
                    <th class="px-4 py-3 text-center">Qty</th>
                    <th class="px-4 py-3 text-center">Tot. Units</th>
                    <th class="px-4 py-3 text-center">Total Amount</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($order_items as $item)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm">
                                {{ $item->id }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $item->type }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                @if ($item->status == 'pending')
                                    <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-yellow-400 rounded-full leading-sm">
                                        PENDING
                                    </div>
                                @elseif ($item->status == 'ordered')
                                    <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-blue-700 uppercase bg-blue-200 rounded-full leading-sm">
                                        ORDERED
                                    </div>
                                @elseif ($item->status == 'preparing')
                                    <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-orange-700 uppercase bg-orange-200 rounded-full leading-sm">
                                        PREPARING
                                    </div>
                                @elseif ($item->status == 'done')
                                    <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-green-700 uppercase bg-green-200 rounded-full leading-sm">
                                        DONE
                                    </div>
                                @elseif ($item->status == 'served')
                                    <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-teal-700 uppercase bg-teal-200 rounded-full leading-sm">
                                        SERVED
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $item->price }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $item->units }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $item->qty }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $item->qty*$item->units }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $item->total_amount }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-4 text-sm">
                                    @if ($item->status != 'served')
                                        <button
                                            class="btn-update-order flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editItemModal"
                                            data-order="{{ json_encode($item) }}"
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="text-gray-700">
                            <td colspan="7" class="px-4 py-3 text-sm text-center">
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

    <x-slot name="scripts">
        <script>
            $('.btn-update-order').on("click", function() {
                var order = JSON.stringify($(this).data('order'));
                Livewire.emit('setItem', order);

            });
        </script>
    </x-slot>

</x-app-layout>
