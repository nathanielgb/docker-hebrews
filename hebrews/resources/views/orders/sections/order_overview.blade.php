<div class="mb-4 flex justify-center space-x-2">
    <button
        @click="isDinein=true"
        id="dinein-toggle"
        class="px-5 py-3 font-medium leading-5 text-white transition-colors duration-150 bg-green-600 border-2 border-transparent rounded-lg active:bg-green-600 focus:outline-none focus:shadow-outline-green"
    >
        Dine-in
    </button>
    <button
        @click="isDinein=false"
        id="takeout-toggle"
        class="px-5 py-3 font-medium leading-5 text-green-600 transition-colors duration-150 bg-white border-2 border-transparent border-green-600 rounded-lg active:bg-green-600 focus:outline-none focus:shadow-outline-green"
    >
        Take-out
</button>
</div>
<div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
    <div class="w-full overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                <th class="px-4 py-4">Name</th>
                <th class="px-4 py-3">Qty</th>
                <th class="px-4 py-3">Amount</th>
                <th class="px-4 py-3 text-center">Action</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y">
                <tr x-show="orderItems == ''" class="text-gray-700">
                    <td colspan="7" class="px-4 py-3 text-sm text-center">
                        No items found.
                    </td>
                </tr>
                <template x-for="ord_item in orderItems">
                    <tr class="text-gray-700">
                        <td class="px-4 py-4 text-sm">
                            <span x-text="ord_item.name"></span>
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <span x-text="ord_item.qty"></span>
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <span x-text="ord_item.total_price"></span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center text-sm">
                                <button
                                    @click="changeOrderType(ord_item)"
                                    :class="ord_item.is_dinein ? 'bg-green-600 active:bg-green-600' : 'bg-yellow-400 active:bg-yellow-600' "
                                    class=" text-white  font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button"
                                    >
                                    <span x-text="ord_item.type"></span>
                                </button>
                                <button
                                    @click="removeOrderItem(ord_item.ord_item_id, ord_item.total_price)"
                                    class="bg-red-600 text-white active:bg-red-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button"
                                >
                                    remove
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t bg-gray-50 sm:grid-cols-9">
        <div class="flex justify-end">

            <p>SUBTOTAL: <span x-text="subTotalPrice"></span></p>
        </div>
    </div>
</div>
<div>
    <div class="w-full">
        <label class="block mb-4 text-sm">
            <span class="text-gray-700 dark:text-gray-400">Select Table/s</span>
            <select
                x-model="transSelectedTables"
                id="select-table"
                name="tables[]"
                placeholder="Select tables..."
                autocomplete="off"
                class="block w-full rounded-sm cursor-pointer focus:outline-none"
                multiple
            >
                <option value="1">Table 1</option>
                <option value="2">Table 2</option>
                <option value="3">Table 3</option>
                <option value="4">Table 4</option>
                <option value="4">Table 4</option>
                <option value="5">Table 5</option>
                <option value="6">Table 6</option>
                <option value="7">Table 7</option>
                <option value="8">Table 8</option>
                <option value="9">Table 9</option>
                <option value="10">Table 10</option>
            </select>
        </label>
        <label class="block mb-4 text-sm">
            <span class="text-gray-700 dark:text-gray-400">Discount</span>
            <select
                x-model="transDiscount"
                id="order-discount"
                class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:outline-none focus:shadow-outline-gray"
            >
                <option value="">None</option>
                <option value="pwd">Person with disability (PWD)</option>
                <option value="senior">Senior Citizen</option>
                <option value="special">Special Discount</option>
            </select>
        </label>
        <label class="block my-4 text-sm">
            <span class="text-gray-700 dark:text-gray-400">Note</span>
            <textarea
                x-model="transNote"
                class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:outline-none focus:shadow-outline-gray"
                rows="3"
                placeholder="Enter some additional note (optional)."
            ></textarea>
        </label>
    </div>

    <div class="flex justify-center">
        <button
            @click="openConfirmModal"
            id="order-confirm"
            data-orders="$data"
            class="px-5 py-3 font-medium leading-5 text-white transition-colors duration-150 bg-green-600 border border-transparent rounded-lg active:bg-green-600 focus:outline-none focus:shadow-outline-green"
        >
            Confirm
        </button>
    </div>
</div>
