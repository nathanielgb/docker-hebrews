<x-app-layout>

    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
    </x-slot>

    <div class="container grid px-6 mx-auto mb-5 space-y-2 md:px-28">
        <div class="flex justify-between">
            <div>
                <h2 class="my-3 text-2xl font-semibold text-gray-700">Report</h2>
            </div>
            <div class="flex items-end">
                <a
                    href="{{ route('orders.report.show') }}"
                    class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                    >
                    <span><i class="fa-solid fa-circle-arrow-left"></i> BACK</span>
                </a>
            </div>
        </div>

        <div class="p-4 bg-white rounded-lg shadow-xs">
            <div class="w-full mb-8 overflow-hidden">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap border border-collapse border-gray-400">
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Date
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                {{ $date_range }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Branch ID
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                {{ $branch_id }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Order/s
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                @php
                                    $order_numbers = $order_numbers ?? [];
                                @endphp
                                <button
                                    class="btn-details inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-blue-600 rounded-lg leading-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#showDetails"
                                    data-details="{{ json_encode($order_numbers) }}"
                                    data-field="Order/s"
                                    >
                                    <i class="fa-solid fa-eye"></i>&nbsp;SHOW
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Customer
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                {{ $customers }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Status
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                @if ($status == 'cancelled')
                                    <span class="font-bold text-red-600">Void</span>
                                @elseif ($status == 'completed')
                                    <span class="font-bold text-green-600">Completed</span>
                                @elseif ($status == 'confirmed')
                                    <span class="font-bold text-blue-600">Confirmed ({{ $order->confirmed_by }})</span>
                                @elseif ($status == 'pending')
                                    <span class="font-bold text-yellow-400">Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Number of Orders
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                {{ $order_count }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Order Subtotal
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                {{ $orders_subtotal }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Discount Amount
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                {{ $orders_discount }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Total Amount
                            </td>
                            <td class="w-1/2 p-4 font-bold text-left text-green-600 border border-gray-300">
                                {{ $orders_total }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Total Expenses
                            </td>
                            <td class="w-1/2 p-4 font-bold text-left text-red-600 border border-gray-300">
                                -{{ $total_expense }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Net Profit
                            </td>
                            @if ($profit < 0)
                                <td class="w-1/2 p-4 font-bold text-left text-red-600 border border-gray-300">
                                    {{ $profit }}
                                </td>
                            @else
                                <td class="w-1/2 p-4 font-bold text-left text-green-600 border border-gray-300">
                                    {{ $profit }}
                                </td>
                            @endif
                        </tr>
                    </table>
                </div>
            </div>
            <div class="flex flex-col">
                <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                        <div class="overflow-hidden">
                            <table class="min-w-full text-center border">
                                <thead class="border-b">
                                    <tr>
                                        <th scope="col" colspan="6" class="px-6 py-4 text-gray-900 border-b ">Order Items Summary</th>
                                    </tr>
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-sm font-medium text-gray-900 border-r">
                                            Product Name
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-sm font-medium text-gray-900 border-r">
                                            Type
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-sm font-medium text-gray-900 border-r">
                                            Order Qty
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-sm font-medium text-gray-900 border-r">
                                            Total Qty
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-sm font-medium text-gray-900 border-r">
                                            Inventory
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-sm font-medium text-gray-900">
                                            Subtotal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order_items as $item)
                                        <tr class="border-b">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900 border-r whitespace-nowrap">{{ $item->name }}</td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900 border-r whitespace-nowrap">Menu Item</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 border-r whitespace-nowrap">
                                                {{ $item->total_qty }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 border-r whitespace-nowrap">
                                                {{ $item->stock_used }}
                                                @if ($item->unit_label)
                                                    ({{ $item->unit_label }})
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 border-r whitespace-nowrap">
                                                @if ($item->inventory_id)
                                                    <ul>
                                                        <li>Name: {{ $item->inventory_name }}</li>
                                                        <li>Code: {{ $item->inventory_code }}</li>
                                                        <li>Used: {{ $item->stock_used }}</li>
                                                    </ul>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                {{ $item->total_amount }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    @foreach ($addon_order_items as $addon)
                                        <tr class="border-b">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900 border-r whitespace-nowrap">{{ $addon->inventory_name }}</td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900 border-r whitespace-nowrap">Addon Item</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 border-r whitespace-nowrap">
                                                {{ $addon->orderItem->qty }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 border-r whitespace-nowrap">
                                                {{ $addon->stock_used }}
                                                @if ($addon->unit_label)
                                                    ({{ $item->unit_label }})
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 border-r whitespace-nowrap">
                                                @if ($addon->inventory_id)

                                                    <ul>
                                                        <li>Name: {{ $addon->inventory_name }}</li>
                                                        <li>Code: {{ $addon->inventory_code }}</li>
                                                        <li>Used: {{ $addon->stock_used }}</li>
                                                    </ul>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                0.00
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('order_reports.partials.modals.details')

    <x-slot name="scripts">
        <script>
            $('.btn-details').on("click", function() {
                var field = JSON.stringify($(this).data('field'));
                var details = JSON.stringify($(this).data('details'));
                console.log(details)
                field = field.slice(1, -1);
                Livewire.emit('setItem', field, details);
            });
        </script>
    </x-slot>

</x-app-layout>
