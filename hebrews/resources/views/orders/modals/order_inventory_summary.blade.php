<!-- Modal -->
<div class="fixed top-0 left-0 hidden w-full h-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 outline-none modal fade"
    id="inventorySummaryModal"
    tabindex="-1"
    aria-labelledby="inventorySummaryModalLabel"
    aria-hidden="true"
    >
    <div class="relative w-auto pointer-events-none modal-dialog modal-dialog-centered">
        <div
            class="relative flex flex-col w-full text-current bg-white border-none rounded-md shadow-lg outline-none pointer-events-auto modal-content bg-clip-padding"
            >
            <div
                class="flex items-center justify-between flex-shrink-0 p-4 border-b border-gray-200 modal-header rounded-t-md"
                >
                <h5 class="text-xl font-medium leading-normal text-gray-800" id="inventorySummaryModalLabel">
                    Inventories Used
                </h5>
                <button type="button"
                    class="box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 btn-close focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>
            <div class="relative p-4 modal-body">
                <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
                    <div class="w-full overflow-x-auto">
                        @if (!$order->confirmed)
                            <table class="w-full whitespace-no-wrap">
                                <thead>
                                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Running Stock</th>
                                    <th class="px-4 py-3">Total Used</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y">
                                    @forelse ($inventoriesUsed as $iv)
                                        @if ($iv['running_stock'] < $iv['total_used'])
                                            <tr class="text-red-700">
                                        @else
                                            <tr class="text-gray-700">
                                        @endif
                                            <td class="px-4 py-3 text-sm text-s">
                                                {{ $iv['name'] }}<br>
                                                <span class="text-small"><em>({{ $iv['inventory_code'] }})</em></span>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                {{ $iv['running_stock'] }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                {{ $iv['total_used'] }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="text-gray-700">
                                            <td colspan="3" class="px-4 py-3 text-sm text-center">
                                                No records found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        @else
                            <table class="w-full whitespace-no-wrap">
                                <thead>
                                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Total Used</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y">
                                    @forelse ($inventoriesUsed as $iv)
                                        <tr class="text-gray-700">
                                            <td class="px-4 py-3 text-sm text-s">
                                                {{ $iv['name'] }}<br>
                                                <span class="text-small"><em>({{ $iv['inventory_code'] }})</em></span>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                {{ $iv['total_used'] }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="text-gray-700">
                                            <td colspan="3" class="px-4 py-3 text-sm text-center">
                                                No records found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
