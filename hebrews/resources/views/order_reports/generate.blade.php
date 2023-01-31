<x-app-layout>
    <x-slot name="styles">
        <link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">
    </x-slot>

    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
    </x-slot>

    <div class="container grid px-6 mx-auto space-y-2 md:px-28" style="max-width: 850px;">
        <h2 class="my-3 text-2xl font-semibold text-gray-700">Filter Orders</h2>
        @include('components.alert-message')

        <div class="p-4 bg-white rounded-lg shadow-xs">
            <form action="{{ route('orders.report.generate') }}" method="get" autocomplete="off">
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Date</span>
                    <input name="date" class="styled-input" type="text" placeholder="Enter date range">
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Order Number/s</span>
                    <input class="styled-input" name="order_id" type="text" placeholder="Enter order number/s">
                    <p class="text-xs text-yellow-500">note: you can search for single or multiple order numbers thru comma separated i/e. "432,123,567"</p>
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Order status</span>
                    <select class="styled-input" name="status">
                        <option value="" selected disabled>Select status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Branch</span>
                    <select class="styled-input" name="branch_id">
                        <option value="" selected disabled>Select a branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" >{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Server name</span>
                    <select class="styled-input" name="servername">
                        <option value="" selected disabled>Select a user</option>
                        @foreach ($admins as $admin)
                            <option value="{{ $admin }}">{{ $admin }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Customer name</span>
                    <select class="styled-input" name="customer_name">
                        <option value="" selected disabled>Select a customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer }}">{{ $customer }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="flex justify-center space-x-4">
                    <button
                        type="submit"
                        class="inline-block px-6 py-2.5 font-medium leading-tight text-white uppercase transition duration-150 ease-in-out bg-green-800 rounded shadow-lg text-s hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg"
                        >
                        GENERATE
                    </button>
                </div>
            </form>
        </div>
    </div>

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
                        format: 'YYYY/MM/DD'
                    }
                });
            });
        </script>
    </x-slot>

</x-app-layout>
