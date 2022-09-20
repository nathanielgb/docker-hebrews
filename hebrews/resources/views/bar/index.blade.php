<x-app-layout>
    <x-slot name="header">
        {{ __('Bar Orders') }}
    </x-slot>

    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
    </x-slot>
        @include('components.alert-message')

        @forelse ($orders as $order)
            <div class="w-full mt-4 mb-8 overflow-hidden border rounded-lg shadow-xs">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                        <tr class="text-sm font-semibold tracking-wide text-left text-gray-500 uppercase bg-white border-b">
                            <th colspan="3" class="px-4 py-3 text-center">
                                <div class="flex flex-col">
                                    <span class="text-left">
                                        Table/s:
                                        @foreach ($order->table as $table)
                                            <span>{{ $table }}@if(!$loop->last),@endif</span>
                                        @endforeach
                                    </span>
                                </div>
                            </th>
                            <th colspan="2" class="px-4 py-3">
                                <div class="flex flex-col">
                                    <span class="text-right">
                                        ORDER ID: {{ $order->order_id }}
                                    </span>
                                    <span class="text-right">
                                        <em>
                                            {{ Carbon\Carbon::parse($order->updated_at)->format('M-d-Y g:i:s A') }}
                                        </em>
                                    </span>
                                </div>
                            </th>
                        </tr>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3 text-center">Qty</th>
                            <th class="px-2 py-3 text-center">Type</th>
                            <th class="px-2 py-3 text-center">Status</th>
                            <th class="px-2 py-3 text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            @forelse ($order->items as $item)
                                <tr class="text-gray-700">
                                    <td class="px-4 py-3 text-sm">
                                        {{ $item->name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        {{ $item->qty }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        {{ $item->order_type }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
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
                                        @endif
                                    </td>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col items-center space-y-2 text-sm">
                                                @if ($item->status === 'preparing')
                                                    <button
                                                        @click="openModal, completeBarItemId='{{ $item->id }}', completeBarItemName='{{ $item->name }}'"
                                                        class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-green-600 border border-transparent rounded-lg active:bg-green-600 hover:bg-green-700 focus:outline-none focus:shadow-outline-green"
                                                    >
                                                        Done
                                                    </button>
                                                @elseif ($item->status === 'ordered')
                                                    <a
                                                        href="{{ route('bar.order.prepare', $item->id) }}"
                                                        class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                                                    >
                                                        Prepare
                                                    </a>
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
        @include('bar.modals.done')

</x-app-layout>
