<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('data', {
                    data: [],
                })
            })
        </script>
    </x-slot>

    <x-slot name="header">
        {{ __('Logs - Inventory') }}
    </x-slot>

    @include('components.alert-message')

    <div class="flex justify-between my-3">
        <div>
            {{-- <a
                href="{{ route('menu.index') }}"
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                >
                <span><i class="fa-solid fa-circle-arrow-left"></i> BACK</span>
            </a> --}}
        </div>

        <div class="flex space-x-2 jusify-center">
            <a
                href="{{ route('logs.inventory.index') }}"
                class="flex items-center inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                >
                <span><i class="fa-solid fa-list"></i> VIEW ALL</span>
            </a>

            <button
                type="button"
                class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                data-bs-toggle="modal"
                data-bs-target="#searchModal"
                >
                <i class="fa-solid fa-magnifying-glass"></i> SEARCH
            </button>
        </div>

    </div>

    <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Module</th>
                    <th class="px-4 py-3">Inventory</th>
                    <th class="px-4 py-3">Total Qty</th>
                    <th class="px-4 py-3">Previous Stock</th>
                    <th class="px-4 py-3">Current Stock</th>
                    <th class="px-4 py-3">Created At</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($logs as $log)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3 text-sm text-s">
                                {{ $log->id }}
                            </td>
                            <td class="px-4 py-3 text-sm text-s">
                                {{ $log->title }}
                            </td>
                            <td class="px-4 py-3 text-sm text-s">
                                {{ $log->data['order_id'] ?? '' }}<br>
                                {{ $log->data['module'] ?? '' }}<br>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <ul>
                                    @if (isset($log->data['inventory']['branch_name']))
                                        <li>branch:
                                            <span class="font-bold">
                                                {{ $log->data['inventory']['branch_name'] ?? '' }}
                                            </span>
                                        </li>
                                    @endif
                                    <li>name:
                                        <span class="font-bold">
                                            {{ $log->data['inventory']['name'] ?? '' }}
                                        </span>
                                    </li>
                                    <li>code:
                                        <span class="font-bold">
                                            {{ $log->data['inventory']['inventory_code'] ?? '' }}
                                        </span>
                                    </li>
                                    <li>unit:
                                        <span class="font-bold">
                                            {{ $log->data['inventory']['unit_label'] ?? '' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                            <td class="px-4 py-3 text-sm text-s">
                                {{ $log->data['inventory']['total_qty'] ?? '' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-s">
                                {{ $log->data['inventory']['stock_before'] ?? '' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-s">
                                {{ $log->data['inventory']['stock_after'] ?? '' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ Carbon\Carbon::parse($log->created_at)->format('M-d-Y g:i A') }}
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
        {{-- @if ($inventory_items->hasPages())
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
                {{ $inventory_items->withQueryString()->links() }}
            </div>
        @endif --}}
    </div>

    @include('logs.inventory.modals.search')

</x-app-layout>
