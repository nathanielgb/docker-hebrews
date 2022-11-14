<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('transactions', {
                    data: []
                })
            })
        </script>
    </x-slot>

    <x-slot name="header">
        {{ __('Transactions') }}
    </x-slot>

    @include('components.alert-message')

    <div class="flex justify-between my-3">
        <div>
            <a
                href="{{ route('customers.index') }}"
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
                    <th class="px-4 py-3">Order ID</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Type</th>
                    <th class="px-4 py-3">Total Amount</th>
                    <th class="px-4 py-3">Transaction Date</th>
                    <th class="px-4 py-3"></th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($transactions as $item)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm">
                                {{ $item->order_id }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                @if ($item->cancelled)
                                    <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                        VOID
                                    </div>
                                @elseif ($item->completed)
                                    <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-green-600 rounded-full leading-sm">
                                        COMPLETED
                                    </div>
                                @elseif ($item->confirmed)
                                    <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-blue-600 rounded-full leading-sm">
                                        CONFIRMED
                                    </div>
                                @else
                                    <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-yellow-400 rounded-full leading-sm">
                                        PENDING
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                @if ($item->order_type == 'dinein')
                                    <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Dine-in</span>
                                @else
                                    <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Take-out</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->total_amount }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ Carbon\Carbon::parse($item->created_at)->format('M-d-Y g:i A') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <button
                                    class="flex items-center inline-block px-4 py-1.5 bg-green-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-500 hover:shadow-lg focus:bg-green-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-500 active:shadow-lg transition duration-150 ease-in-out"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $item->id }}"
                                    aria-expanded="false"
                                    aria-controls="collapse{{ $item->id }}"
                                    >
                                    <span><i class="fa-solid fa-info"></i></span>
                                </button>
                            </td>
                        </tr>
                        <tr class="collapse" id="collapse{{ $item->id }}">
                            <td colspan="8">
                                <div class="flex text-sm" style="margin: 15px; justify-content: space-around;">
                                    <div class="flex flex-col ml-3 mr-3">
                                        <div class="mb-2 row">
                                            <span class="">Order Number: {{ $item->order_id }}</span>
                                        </div>
                                        <div class="mb-2 row">
                                            Customer ID: {{ $item->customer_id }}
                                        </div>
                                        <div class="mb-2 row">
                                            Customer Name: {{ $item->customer_name }}
                                        </div>
                                        <div class="mb-2 row">
                                            Status:
                                            @if ($item->cancelled)
                                                <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                                    VOID
                                                </div>
                                            @elseif ($item->completed)
                                                <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-green-600 rounded-full leading-sm">
                                                    COMPLETED
                                                </div>
                                            @elseif ($item->confirmed)
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
                                            <span class="">Subtotal: {{ $item->subtotal }}</span>
                                        </div>
                                        <div class="mb-2 row">
                                            Fees: {{ $item->fees }}
                                        </div>
                                        <div class="mb-2 row">
                                            Discount Amount: {{ $item->discount_amount }}
                                        </div>
                                        <div class="mb-2 row">
                                            Discount Type: {{ $item->discount_type . ' (' . $item->discount_unit . ')' ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="flex flex-col ml-3 mr-3">
                                        <div class="mb-2 row">
                                            <span class="">Total Amount Due: {{ $item->total_amount }}</span>
                                        </div>
                                        <div class="mb-2 row">
                                            Deposit Balance: {{ $item->deposit_bal ?? 0.00 }}
                                        </div>
                                        <div class="mb-2 row">
                                            Cash Given: {{ $item->amount_given }}
                                        </div>
                                        <div class="mb-2 row">
                                            Remaining Balance :
                                            @if ($item->remaining_bal < 0)
                                                <span class="text-red-600">{{ $item->remaining_bal ?? 'N/A' }}</span>
                                            @else
                                                <span class="text-green-600">{{ $item->remaining_bal ?? 'N/A' }} (change)</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-col ml-3 mr-3">
                                        <div class="mb-2 row">
                                            Order Type:
                                            @if ($item->order_type == 'dinein')
                                                <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Dine-in</span>
                                            @else
                                                <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-400 text-white rounded">Take-out</span>
                                            @endif
                                        </div>
                                        <div class="mb-2 row">
                                            @if ($item->order_type == 'dinein')
                                                Table/s:
                                                @if ($item->table)
                                                @foreach ($item->table as $table)
                                                    <span>{{ $table }}@if(!$loop->last),@endif</span>
                                                @endforeach
                                            @endif
                                            @else
                                                Delivery Method: {{ $item->delivery_method }}
                                            @endif
                                        </div>
                                        <div class="mb-2 row">
                                            Confirmed by: {{ $item->confirmed_by ?? 'N/A' }}
                                        </div>
                                        <div class="mb-2 row">
                                            Credited by: {{ $item->credited_by ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="flex flex-col ml-3 mr-3">
                                        <div class="mb-2 row">
                                            Created at: {{ Carbon\Carbon::parse($item->created_at)->format('M-d-Y g:i A') }}
                                        </div>
                                        <div class="mb-2 row">
                                            Updated at: {{ Carbon\Carbon::parse($item->updated_at)->format('M-d-Y g:i A') }}
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
        @if ($transactions->hasPages())
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                {{ $transactions->withQueryString()->links() }}
            </div>
        @endif
    </div>
    @include('bank_accounts.modals.zeroout_account')
</x-app-layout>
