<div wire:poll.1s="updateData">
    <span> Current time: {{ now() }}</span>
    @forelse ($orders as $order)
        <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-sm font-semibold tracking-wide text-left text-gray-500 uppercase bg-white border-b">
                            <th colspan="6" class="px-4 py-3 text-center">
                                <div class="flex flex-col">
                                    <span class="text-left">
                                        Table/s:
                                        @if ($order->table)
                                            @foreach ($order->table as $table)
                                                <span>{{ $table }}@if(!$loop->last),@endif</span>
                                            @endforeach
                                        @endif
                                    </span>
                                    <span class="text-left">
                                        ORDER ID: {{ $order->order_id }}
                                    </span>
                                    <span class="text-left">
                                        <em>
                                            {{ Carbon\Carbon::parse($order->updated_at)->format('M-d-Y g:i:s A') }}
                                        </em>
                                    </span>
                                </div>
                            </th>
                            <th colspan="1" class="px-4 py-3">
                                <div class="flex justify-end">
                                    <span class="text-right">
                                        <button
                                            class="flex items-center inline-block px-6 py-2.5 bg-gray-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-600 hover:shadow-lg focus:bg-gray-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-600 active:shadow-lg transition duration-150 ease-in-out"
                                            data-bs-toggle="modal"
                                            data-bs-target="#clearOrderItemModal"
                                            @click="$store.data.orderId='{{ $order->order_id }}'"
                                            >
                                            <i class="fa-solid fa-circle-check"></i>&nbsp;Clear
                                        </button>
                                    </span>
                                </div>
                            </th>
                        </tr>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                            <th class="px-6 py-3">Name</th>
                            <th class="px-2 py-3 text-center">O.Type</th>
                            <th class="px-4 py-4 text-center">Addons</th>
                            <th class="px-2 py-3">Qty</th>
                            <th class="px-2 py-3">Note</th>
                            <th class="px-2 py-3 text-center">Status</th>
                            <th class="px-2 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @forelse ($order->items as $item)
                            <tr class="text-gray-700">
                                <td class="px-6 py-3 text-sm">
                                    {{ $item->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    @if (isset($item->data['is_dinein']) && $item->data['is_dinein'])
                                        <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Dine-in</span>
                                    @else
                                        <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Take-out</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-center">
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
                                </td>
                                <td class="px-2 py-3 text-sm" style="min-width:160px; max-width: 200px;">
                                    <ul>
                                        <li>
                                            @if ($item->inventory_code)
                                                <em class="font-bold">{{ $item->inventory_code }}</em>
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

                                <td class="px-2 py-3 text-sm" style="min-width:100px; max-width: 150px;">
                                    <p>
                                        {{ $item->note }}
                                    </p>
                                </td>
                                <td class="px-2 py-3 text-sm text-center">
                                    @if ($item->status == 'ordered')
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
                                    @elseif ($item->status == 'void')
                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                            VOID
                                        </div>
                                    @elseif ($item->status == 'served')
                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-teal-700 uppercase bg-teal-200 rounded-full leading-sm">
                                            SERVED
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex flex-col items-center space-y-2 text-sm">
                                        @if ($item->status != 'void')
                                            @if ($item->status === 'preparing')
                                                <button
                                                    class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-600 hover:shadow-lg focus:bg-green-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-600 active:shadow-lg transition duration-150 ease-in-out"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#doneOrderItemModal"
                                                    @click="completeKitchenItemId='{{ $item->id }}', completeKitchenItemName='{{ $item->name }}'"
                                                    >
                                                    Complete
                                                </button>
                                            @elseif ($item->status === 'ordered')
                                                <a
                                                    href="{{ route('production.order.prepare', $item->id) }}"
                                                    class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"

                                                >
                                                    Prepare
                                                </a>
                                            @endif
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
            @if ($order->note)
                <div class="flex flex-col justify-center px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-white border-t sm:grid-cols-9">
                    <h6>Note:</h6>
                    <p>{{ $order->note }}</p>
                </div>
            @endif
        </div>
    @empty
        <div class="p-4 mt-4 text-center bg-white rounded-lg shadow-xs">
            No pending orders.
        </div>
    @endforelse

</div>
