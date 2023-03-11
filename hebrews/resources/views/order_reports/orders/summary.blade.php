<x-app-layout>
    <x-slot name="headerscript">
        <script>

        </script>
    </x-slot>

    <x-slot name="header">
        {{ __('Order Report') }}
    </x-slot>

    @include('components.alert-message')

    <div class="flex justify-between my-3">
        <div class="flex items-end">
            <a
                href="{{ route('orders.report.show') }}"
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                >
                <span><i class="fa-solid fa-circle-arrow-left"></i> BACK</span>
            </a>
        </div>
        <button
            type="button"
            class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
            data-bs-toggle="modal"
            data-bs-target="#exportModal"
            >
            <i class="fa-solid fa-file-export"></i> EXPORT
        </button>
    </div>

    <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Order Id</th>
                    <th class="px-4 py-3">Branch Id</th>
                    <th class="px-4 py-3">Customer Name</th>
                    <th class="px-4 py-3 text-center">Type</th>
                    <th class="px-4 py-3 text-center">Total Amount</th>
                    <th class="px-4 py-3">Created at</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($orders as $order)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm">
                                {{ $order->order_id }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $order->branch_id }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $order->customer_name }}
                            </td>
                            <td class="text-center">
                                @if ($order->order_type == 'dinein')
                                    <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Dine-in</span>
                                @else
                                    <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Take-out</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $order->total_amount }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ Carbon\Carbon::parse($order->created_at)->format('M-d-Y g:i A') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-4 text-sm">
                                    <button
                                        class="flex items-center inline-block px-4 py-2.5 bg-green-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-500 hover:shadow-lg focus:bg-green-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-500 active:shadow-lg transition duration-150 ease-in-out"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $order->order_id }}"
                                        aria-expanded="false"
                                        aria-controls="collapse{{ $order->order_id }}"
                                        >
                                        <span><i class="fa-solid fa-info"></i></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="collapse" id="collapse{{ $order->order_id }}">
                            <td colspan="11">
                                <div class="flex text-sm" style="margin: 15px; justify-content: space-around;">
                                    <div class="flex flex-col ml-3 mr-3">
                                        <div class="mb-2 row">
                                            <span class="">Order Number: {{ $order->order_id }}</span>
                                        </div>
                                        <div class="mb-2 row">
                                            Customer ID: {{ $order->customer_id }}
                                        </div>
                                        <div class="mb-2 row">
                                            Customer Name: {{ $order->customer_name }}
                                        </div>
                                        <div class="mb-2 row">
                                            Status:
                                            @if ($order->cancelled)
                                                <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                                    VOID
                                                </div>
                                            @elseif ($order->completed)
                                                <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-green-600 rounded-full leading-sm">
                                                    COMPLETED
                                                </div>
                                            @elseif ($order->confirmed)
                                                <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-blue-600 rounded-full leading-sm">
                                                    CONFIRMED
                                                </div>
                                            @else
                                                <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-yellow-400 rounded-full leading-sm">
                                                    PENDING
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-col ml-3 mr-3">
                                        <div class="mb-2 row">
                                            <span class="">Subtotal: {{ $order->subtotal }}</span>
                                        </div>
                                        <div class="mb-2 row">
                                            Fees: {{ $order->fees }}
                                        </div>
                                        <div class="mb-2 row">
                                            Discount Amount: {{ $order->discount_amount }}
                                        </div>
                                        <div class="mb-2 row">
                                            Discount Type: {{ $order->discount_type . ' (' . $order->discount_unit . ')' ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="flex flex-col ml-3 mr-3">
                                        <div class="mb-2 row">
                                            <span class="">Total Amount Due: {{ $order->total_amount }}</span>
                                        </div>
                                        <div class="mb-2 row">
                                            Deposit Balance: {{ $order->deposit_bal ?? 0.00 }}
                                        </div>
                                        <div class="mb-2 row">
                                            Cash Given: {{ $order->amount_given }}
                                        </div>
                                        <div class="mb-2 row">
                                            Remaining Balance :
                                            @if ($order->remaining_bal < 0)
                                                <span class="text-red-600">{{ $order->remaining_bal ?? 'N/A' }}</span>
                                            @else
                                                <span class="text-green-600">{{ $order->remaining_bal ?? 'N/A' }} (change)</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-col ml-3 mr-3">
                                        <div class="mb-2 row">
                                            Order Type:
                                            @if ($order->order_type == 'dinein')
                                                <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Dine-in</span>
                                            @else
                                                <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Take-out</span>
                                            @endif
                                        </div>
                                        <div class="mb-2 row">
                                            @if ($order->order_type == 'dinein')
                                                Table/s:
                                                @if ($order->table)
                                                    @foreach ($order->table as $table)
                                                        <span>{{ $table }}@if(!$loop->last),@endif</span>
                                                    @endforeach
                                                @endif
                                            @else
                                                Delivery Method: {{ $order->delivery_method }}
                                            @endif
                                        </div>
                                        <div class="mb-2 row">
                                            Confirmed by: {{ $order->confirmed_by ?? 'N/A' }}
                                        </div>
                                        <div class="mb-2 row">
                                            Credited by: {{ $order->credited_by ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="flex flex-col ml-3 mr-3">
                                        <div class="mb-2 row">
                                            Created at: {{ Carbon\Carbon::parse($order->created_at)->format('M-d-Y g:i A') }}
                                        </div>
                                        <div class="mb-2 row">
                                            Updated at: {{ Carbon\Carbon::parse($order->updated_at)->format('M-d-Y g:i A') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="px-6"  style="margin: 15px;">
                                    <h5>Order Items</h5>
                                    <div class="w-full mb-8 border rounded-lg shadow-xs mxl:overflow-hidden">
                                        <div class="w-full overflow-x-auto">
                                            <table class="w-full whitespace-no-wrap">
                                                <thead>
                                                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                                    <th class="px-4 py-3">Name</th>
                                                    <th class="px-4 py-3 text-center">O.Type</th>
                                                    <th class="px-4 py-3">Inventory</th>
                                                    <th class="px-4 py-3">Order Qty</th>
                                                    <th class="px-4 py-3">Total Qty</th>
                                                    <th class="px-4 py-3">Total Amount</th>
                                                    <th class="px-4 py-3">Addons</th>
                                                </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y">
                                                    @foreach($order->items as $item)
                                                        <tr>
                                                            <td class="px-4 py-3 text-sm">
                                                                {{ $item->name }}
                                                            </td>
                                                            <td class="px-4 py-3 text-sm text-center">
                                                                @if (isset($item->data['is_dinein']) && $item->data['is_dinein'])
                                                                    <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Dine-in</span>
                                                                @else
                                                                    <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Take-out</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-4 py-3 text-sm">
                                                                {{ $item->inventory_code }}
                                                            </td>
                                                            <td class="px-4 py-3 text-sm">
                                                                {{ $item->qty }}
                                                            </td>
                                                            <td class="px-4 py-3 text-sm">
                                                                {{  $item->qty * $item->units }}
                                                                @if ($item->unit_label)
                                                                    ({{ $item->unit_label }})
                                                                @endif
                                                            </td>
                                                            <td class="px-4 py-3 text-sm">
                                                                {{ $item->total_amount }}
                                                            </td>
                                                            <td class="px-4 py-3 text-sm">
                                                                <ul class="list-disc">
                                                                    @foreach ($item->addons as $addon)
                                                                        <li>{{ $addon->inventory_name }} ({{ $addon->qty     }})</li>
                                                                    @endforeach
                                                                 </ul>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

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
        @if ($orders->hasPages())
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </div>
    @include('order_reports.orders.partials.modals.export')
</x-app-layout>
