<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('expense', {
                    data: []
                })
            })
        </script>
    </x-slot>

    <x-slot name="styles">
        <link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">
    </x-slot>

    <x-slot name="header">
        {{ __('Expense Reports') }}
    </x-slot>

    @include('components.alert-message')

    <div class="flex items-end justify-between my-3">
        <div>
            <ul>
                <li>Total Expense: <span class="font-bold">{{ number_format($total_expense, 2) }}</span></li>
                <li>Date Range:
                    <span class="font-bold">
                        @if (request()->date)
                            {{ request()->date }}
                        @else
                            {{ $date_range }}
                        @endif
                    </span>
                </li>
            </ul>
        </div>
        <div class="flex space-x-2 jusify-center" style="height: 35px;">
            <button
                type="button"
                class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                data-bs-toggle="modal"
                data-bs-target="#searchModal"
                >
                <i class="fa-solid fa-magnifying-glass"></i> SEARCH
            </button>
            <button
                type="button"
                class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                data-bs-toggle="modal"
                data-bs-target="#expenseModal"
                >
                <i class="fa-solid fa-circle-plus"></i> ADD
            </button>
        </div>

    </div>

    <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="px-4 py-3">Created at</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($expenses as $item)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm">
                                {{ $item->id }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->name }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $item->amount }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ Carbon\Carbon::parse($item->created_at)->format('M-d-Y g:i A') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-4 text-sm">
                                    <button
                                        type="button"
                                        class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteExpenseModal"
                                        @click="$store.expense.data={{ json_encode([
                                            'id' => $item->id,
                                            'name' => $item->name,
                                        ]) }}"
                                        >
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
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
        @if ($expenses->hasPages())
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                {{ $expenses->withQueryString()->links() }}
            </div>
        @endif
    </div>
    @include('expense_reports.modal.add_expense')
    @include('expense_reports.modal.search')
    @include('expense_reports.modal.delete')
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
                    $('input[name="date"]').val('all');
                });
            });
        </script>
    </x-slot>
</x-app-layout>
