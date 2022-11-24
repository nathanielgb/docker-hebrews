<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
    </x-slot>

    <x-slot name="styles">
        <link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">
    </x-slot>

    <x-slot name="header">
        {{ __('Orders') }}
    </x-slot>

    @include('components.alert-message')

    {{-- <div class="flex justify-end my-3">
        <button @click="openModal" class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 -ml-1" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            <span>ADD</span>
        </button>
    </div> --}}


    <div class="flex justify-end my-3">
        <div class="flex space-x-2 jusify-center">
            <a href="{{ route('order.list', ['status'=>'all']) }}" class="flex items-center inline-block px-6 py-2.5 bg-purple-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-purple-700 hover:shadow-lg focus:bg-purple-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-purple-800 active:shadow-lg transition duration-150 ease-in-out"
            >
                <span>VIEW ALL</span>
            </a>
            <a href="{{ route('order.list', ['filter'=>'pending']) }}" class="flex items-center inline-block px-6 py-2.5 bg-yellow-400 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-yellow-500 focus:outline-none active:bg-yellow-500 active:shadow-lg transition duration-150 ease-in-out">
                <span>VIEW PENDING</span>
            </a>
            <a href="{{ route('order.list', ['filter'=>'completed']) }}" class="flex items-center inline-block px-6 py-2.5 bg-orange-30 text-white font-medium text-xs leading-tight uppercase rounded shadow-md bg-green-600  hover:bg-green-700 focus:outline-none active:bg-green-60 active:shadow-lg transition duration-150 ease-in-out">
                <span>VIEW COMPLETED</span>
            </a>
            <button
                type="button"
                class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                data-bs-toggle="modal"
                data-bs-target="#searchModal"
                >
                <i class="fa-solid fa-magnifying-glass"></i> SEARCH
            </button>
            {{-- <button @click="openSearchModal" class="flex items-center justify-between px-2 py-2 text-xs font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 -ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <span>SEARCH</span>
            </button> --}}
        </div>

    </div>


    <div class="w-full mb-8 border rounded-lg shadow-xs mxl:overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Order Id</th>
                    <th class="px-4 py-3">Branch Id</th>
                    <th class="px-4 py-3">Customer Name</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Type</th>
                    <th class="px-4 py-3 text-center">Total Amount</th>
                    <th class="px-4 py-3">Updated at</th>
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
                            <td class="px-4 py-3 text-sm text-center">
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
                                {{ Carbon\Carbon::parse($order->updated_at)->format('M-d-Y g:i A') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-4 text-sm">
                                    <a
                                        href="{{ route('order.show_summary',$order->order_id) }}"
                                        class="flex items-center inline-block px-6 py-2.5 bg-green-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-500 hover:shadow-lg focus:bg-green-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-500 active:shadow-lg transition duration-150 ease-in-out"
                                        >
                                        <span><i class="fa-solid fa-eye"></i> View</span>
                                    </a>
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
        @if ($orders->hasPages())
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
    @include('orders.modals.search_list')
    <x-slot name="scripts">
        <script src="{{ asset('js/moment.js') }}"></script>
        <script src="{{ asset('js/daterangepicker.js') }}"></script>
        <script type="text/javascript">
            $(function() {
                $('input[name="date"]').daterangepicker({
                    autoUpdateInput: true,
                    drops: 'down',
                    showDropdowns: true,
                    ranges: {
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Last 12 months': [moment().subtract(11, 'months'), moment()]
                    },
                    locale: {
                        format: 'YYYY/MM/DD',
                        cancelLabel: 'Clear'
                    }
                });


                $('input[name="date"]').on('cancel.daterangepicker', function(ev, picker) {
                    $('input[name="date"]').val('');
                });
            });
        </script>
    </x-slot>
</x-app-layout>
