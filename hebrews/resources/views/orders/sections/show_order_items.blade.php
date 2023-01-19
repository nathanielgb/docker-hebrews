<x-app-layout>
    <x-slot name="headerscript">
        <script>
        </script>
    </x-slot>

    <x-slot name="header">
        {{ __('Order Items') }}
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

    <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3 text-center">Order Type</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3">data</th>
                    <th class="px-4 py-3 text-center">Price</th>
                    <th class="px-4 py-3 text-center">Unit/Qty</th>
                    <th class="px-4 py-3 text-center">Qty</th>
                    <th class="px-4 py-3 text-center">Tot. Stock</th>
                    <th class="px-4 py-3 text-center">Total Amount</th>
                    <th class="px-4 py-3 text-center">Note</th>
                    <th class="px-4 py-3 text-center">Served by</th>

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
                                @elseif ($item->status == 'void')
                                    <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                        VOID
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm" style="width: 10%;">
                                @if (isset($item->menu->inventory))
                                    <ul>
                                        <li>Menu:
                                            <span class="font-bold">
                                                {{ $item->menu_id }}
                                            </span>
                                        </li>
                                        <li>Code:
                                            <span class="font-bold">
                                                {{ $item->inventory_code }}
                                            </span>
                                        </li>
                                        <li>Unit:
                                            <span class="font-bold">
                                                {{ $item->unit_label }}
                                            </span>
                                        </li>
                                        <li>From:
                                            <span class="font-bold">
                                                {{ $item->from }}
                                            </span>
                                        </li>
                                    </ul>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $item->price }} ({{ $item->type }})
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
                            <td class="px-4 py-3 text-sm text-center">
                                <p>
                                    {{ $item->note }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                <p>
                                    {{ $item->served_by }}
                                </p>
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

    <x-slot name="scripts">

    </x-slot>

</x-app-layout>
