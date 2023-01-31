<!-- Modal -->
<div class="fixed top-0 left-0 hidden w-full h-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 outline-none modal fade"
    id="transferInventoryModal"
    tabindex="-1"
    aria-labelledby="transferInventoryModalLabel"
    aria-hidden="true"
    >
    <div class="relative w-auto pointer-events-none modal-dialog modal-dialog-centered">
        <div
            class="relative flex flex-col w-full text-current bg-white border-none rounded-md shadow-lg outline-none pointer-events-auto modal-content bg-clip-padding"
            >
            <div
                class="flex items-center justify-between flex-shrink-0 p-4 border-b border-gray-200 modal-header rounded-t-md"
                >
                <h5 class="text-xl font-medium leading-normal text-gray-800" id="transferInventoryModalLabel">
                    Transfer Inventory
                </h5>
                <button type="button"
                    class="box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 btn-close focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>
            <div class="relative p-4 modal-body">
                <form id="transfer-inventory-form" action="{{ route('branch.inventory.transfer') }}" method="post">
                    @csrf
                    <input type="hidden" name="inventory_id" :value="$store.inventory.updateInventoryData?.id">
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Inventory Code</span>
                        <input
                            type="text"
                            class="styled-input--readonly"
                            :value="$store.inventory.updateInventoryData?.inventory_code"
                            aria-label="Inventory code"
                            readonly/>
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Name</span>
                        <input
                            type="text"
                            class="styled-input--readonly"
                            :value="$store.inventory.updateInventoryData?.name"
                            aria-label="Inventory name"
                            readonly/>
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Current Stock</span>
                        <input
                            type="text"
                            class="styled-input--readonly"
                            :value="$store.inventory.updateInventoryData?.stock"
                            aria-label="Inventory stock"
                            readonly/>
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Transfer Stock</span>
                        <input
                            type="text"
                            class="styled-input"
                            placeholder="Number of stocks to transfer"
                            name="transfer_stock"
                            />
                    </label>


                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Branch to transfer</span>
                        <select class="styled-input" name="transfer_branch">
                            <option value="" selected>Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch['id'] }}" :hidden="$store.inventory.updateInventoryData?.branch_id == {{ $branch['id'] }}">{{ $branch['name'] }}</option>
                            @endforeach
                            <option value="dispose">Dispose (Delete)</option>
                            <option value="warehouse">Warehouse</option>
                        </select>
                        <p class="text-xs text-yellow-500">note: Specify the branch and number of units you want to transfer. Otherwise, you can select "dispose" to remove some items.</p>
                    </label>
                </form>
            </div>
            <div
                class="flex flex-wrap items-center justify-end flex-shrink-0 p-4 border-t border-gray-200 modal-footer rounded-b-md"
                >
                <button
                    type="button"
                    class="inline-block px-6 py-2.5 bg-gray-200 text-gray-700 font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-300 hover:shadow-lg focus:bg-gray-300 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-400 active:shadow-lg transition duration-150 ease-in-out"                    data-bs-dismiss="modal"
                    >
                    Close
                </button>
                <button
                    form="transfer-inventory-form"
                    type="submit"
                    class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out ml-1"
                    >
                    Transfer
                </button>
            </div>
        </div>
    </div>
</div>
