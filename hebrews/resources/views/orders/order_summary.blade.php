<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
    </x-slot>

    <x-slot name="styles">
        <link
            href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css"
            rel="stylesheet"
        />
    </x-slot>
    <x-slot name="header">
        {{ __('View Order') }}
    </x-slot>

    <div class="container grid px-6 mx-auto space-y-2">
        @include('components.alert-message')
        <div class="flex justify-start my-3">
            <div>
                <a
                    href="{{ route('order.list') }}"
                    class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                    >
                    <span><i class="fa-solid fa-circle-arrow-left"></i> BACK</span>
                </a>
            </div>
        </div>

        <div class="p-4 overflow-hidden bg-white rounded-lg shadow-xs">
            <div class="flex w-full" style="justify-content:end;">
                <div class="flex mb-2 space-x-2 jusify-center">
                    <a
                        href="{{ route('order.summary.print',['order_id'=>$order->id]) }}"
                        class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                        >
                        <span><i class="fa-solid fa-print"></i> PRINT</span>
                    </a>
                    @if (!$order->paid && !$order->cancelled && !$order->confirmed)
                        @if(auth()->user()->can('access', 'add-order-item-action'))
                            <a
                                href="{{ route('order.show_add_item',['order_id'=>$order->id]) }}"
                                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                >
                                <span><i class="fa-solid fa-circle-plus"></i> ADD</span>
                            </a>

                        @endif
                        @if(auth()->user()->can('access', 'manage-order-item-action'))
                            <a
                                href="{{ route('order.edit_items', $order->id) }}"
                                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                >
                                <span><i class="fa-solid fa-pen"></i> UPDATE</span>
                            </a>
                        @endif
                    @endif
                </div>
            </div>


            <div class="w-full mb-8 border rounded-lg shadow-xs max-lg:overflow-hidden">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-4 text-center">Type</th>
                            <th class="px-4 py-4 text-center">Status</th>
                            <th class="px-4 py-4 text-center">Units/Qty</th>
                            <th class="px-4 py-4 text-center">Qty</th>
                            <th class="px-4 py-4 text-center">Add-ons</th>
                            <th class="px-4 py-3 text-center">Total Amount</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y">

                                @forelse ($order->items as $item)
                                    <tr class="text-gray-700">
                                        <td class="px-4 py-4 text-sm">
                                            <span>{{ $item->id }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            <span>{{ $item->name }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-center">
                                            <span>{{ $item->type }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-center">
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
                                        <td class="px-4 py-4 text-sm text-center">
                                            <span>{{ $item->units }} ({{ $item->unit_label }})</span>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-center">
                                            <span>{{ $item->qty }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-center">
                                            @if ($item->data)
                                                <ul>
                                                    @foreach ($item->data as $addon)
                                                        <li>{{ $addon['name'] }} - {{ $addon['qty'] }}</li>
                                                    @endforeach

                                                </ul>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-sm text-center">
                                            <span>{{ $item->total_amount }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="text-gray-700">
                                        <td colspan="8" class="px-4 py-3 text-sm text-center">
                                            No items found.
                                        </td>
                                    </tr>
                                @endforelse
                        </tbody>
                        @if (!empty($order->items))
                            <tr class="border-t border-gray-300">
                                <td class="px-4 py-2 text-sm font-semibold text-right" colspan="7">
                                    Subtotal
                                </td>
                                <td class="px-4 py-2 text-sm font-semibold text-center" colspan="1">
                                    {{ $order->subtotal }}
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-sm font-semibold text-right" colspan="7">
                                    Other Fees
                                </td>
                                <td class="px-4 py-2 text-sm font-semibold text-center" colspan="1">
                                    {{ $order->fees }}
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-sm font-semibold text-right" colspan="7">
                                    Discount Amount
                                </td>
                                <td class="px-4 py-2 text-sm font-semibold text-center" colspan="1">
                                    -{{ $order->discount_amount }}
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-sm font-semibold text-right" colspan="7">
                                    Total Amount
                                </td>
                                <td class="px-4 py-2 text-sm font-semibold text-center" colspan="1">
                                    {{ $order->total_amount }}
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="flex w-full" style="justify-content:end;">
                <div class="flex mb-2 space-x-2 jusify-center">
                    @if (!$order->paid && !$order->cancelled && !$order->confirmed)
                        @if(auth()->user()->can('access', 'manage-order-item-action'))
                            <button
                                data-url="{{ route('order.edit', $order->id) }}"
                                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                data-bs-toggle="modal"
                                data-bs-target="#editOrderModal"
                                >
                                <span><i class="fa-solid fa-pen"></i> UPDATE</span>
                            </button>
                        @endif
                    @endif
                </div>
            </div>

            <div class="w-full mb-8 overflow-hidden">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap border border-collapse border-gray-400">
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Order ID
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                {{ $order->id }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Server Name
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                {{ $order->server_name }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Customer Name
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                {{ $order->customer_name }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Status
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                @if ($order->cancelled)
                                    <span class="font-bold text-red-600">Void</span>
                                @elseif ($order->completed)
                                    <span class="font-bold text-green-600">Completed</span>
                                @elseif ($order->confirmed)
                                    <span class="font-bold text-blue-600">Confirmed ({{ $order->confirmed_by }})</span>
                                @else
                                    <span class="font-bold text-yellow-400">Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Table/s
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                @if ($order->table)
                                    @foreach ($order->table as $table)
                                    <span>{{ $table }}@if(!$loop->last),@endif</span>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Delivery Method
                            </td>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                {{ $order->delivery_method }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Deposit Balance
                            </td>
                            <td class="w-1/2 p-4 font-bold text-left text-gray-900 border border-gray-300">
                                {{ $order->deposit_bal }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Cash Given
                            </td>
                            <td class="w-1/2 p-4 font-bold text-left text-gray-900 border border-gray-300">
                                {{ $order->amount_given }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Total Amount Given
                            </td>
                            <td class="w-1/2 p-4 font-bold text-left text-green-600 border border-gray-300">
                                {{ $order->confirmed_amount }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Total Balance
                            </td>
                            <td class="w-1/2 p-4 font-bold text-left text-red-600 border border-gray-300">
                                -{{ $order->total_amount }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Remaining Balance
                            </td>
                            <td class="w-1/2 p-4 font-bold text-left text-red-600 border border-gray-300">
                                @if ($order->remaining_bal < 0)
                                    {{ $order->remaining_bal }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 p-4 font-semibold text-left text-gray-900 border border-gray-300">
                                Change
                            </td>
                            <td class="w-1/2 p-4 font-bold text-left text-green-600 border border-gray-300">
                                @if ($order->remaining_bal > 0)
                                    +{{ $order->remaining_bal }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="flex flex-col flex-wrap justify-center mb-4 space-y-4 md:flex-row md:items-end md:space-x-4">
                @if(auth()->user()->can('access', 'manage-order-process-action') && !$order->cancelled)
                    @if (!$order->confirmed)
                        <button
                            id="confirm-order-btn"
                            data-url="{{ route('order.cancel', $order->id) }}"
                            class="text-center inline-block px-6 py-2.5 font-medium leading-tight text-white uppercase transition duration-150 ease-in-out bg-green-800 rounded shadow-lg text-s hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmOrderModal"
                            >
                            CONFIRM
                        </button>
                    @else
                        @if ($order->completed == false && !$order->cancelled && $order->remaining_bal < 0)
                            <a
                                href="{{ route('order.show_payform', $order->id) }}"
                                class="text-center inline-block px-6 py-2.5 font-medium leading-tight text-white uppercase transition duration-150 ease-in-out bg-green-800 rounded shadow-lg text-s hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg"
                                >
                                PAY
                            </a>
                        @endif

                        @if ($order->completed  && !$order->cancelled)
                            <a
                                href="{{ route('print.receipt', $order->id) }}"
                                class="inline-block px-6 py-2.5 font-medium leading-tight text-white uppercase transition duration-150 ease-in-out bg-green-800 rounded shadow-lg text-s hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg"
                                >
                                PRINT
                            </a>
                        @endif
                    @endif
                    @if (!$order->completed && !$order->cancelled && $order->confirmed && $order->remaining_bal > 0)
                        <button
                            id="complete-order-btn"
                            data-url="{{ route('order.complete', $order->id) }}"
                            class="inline-block px-6 py-2.5 font-medium leading-tight text-white uppercase transition duration-150 ease-in-out bg-green-800 rounded shadow-lg text-s hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg"
                            data-bs-toggle="modal"
                            data-bs-target="#completeOrderModal"
                            >
                            COMPLETE
                        </button>
                    @endif
                    @if (!$order->completed && !$order->cancelled)
                        <button
                            id="cancel-order-btn"
                            data-url="{{ route('order.cancel', $order->id) }}"
                            class="inline-block px-6 py-2.5 font-medium leading-tight text-white uppercase transition duration-150 ease-in-out bg-green-800 rounded shadow-lg text-s hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg"
                            data-bs-toggle="modal"
                            data-bs-target="#cancelOrderModal"
                            >
                            CANCEL
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @include('orders.modals.cancel_order')
    @include('orders.modals.edit_order')
    @include('orders.modals.confirm_orderNew')
    @include('orders.modals.complete_order')


    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script type="text/javascript">
            new TomSelect('#select-table', {
                plugins: ['remove_button'],
            });

            $('#cancel-order-btn').on("click", function() {
                var url =$(this).data( "url" );
                $('#cancel-order-form').attr('action', url);
            });
        </script>
    </x-slot>
</x-app-layout>
