<!-- Modal -->
<div class="fixed top-0 left-0 hidden w-full h-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 outline-none modal fade"
    id="updateDiscountModal"
    tabindex="-1"
    aria-labelledby="updateDiscountModalLabel"
    aria-hidden="true"
    >
    <div class="relative w-auto pointer-events-none modal-dialog modal-dialog-centered">
        <div
            class="relative flex flex-col w-full text-current bg-white border-none rounded-md shadow-lg outline-none pointer-events-auto modal-content bg-clip-padding"
            >
            <div
                class="flex items-center justify-between flex-shrink-0 p-4 border-b border-gray-200 modal-header rounded-t-md"
                >
                <h5 class="text-xl font-medium leading-normal text-gray-800" id="updateDiscountModalLabel">
                    Update
                </h5>
                <button type="button"
                    class="box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 btn-close focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>
            <div class="relative p-4 modal-body">
                <form id="update-discount-form" action="{{ route('discount.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="discount_id" :value="$store.discount.updateData.id">
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Name</span>
                        <input class="styled-input" name="name" type="text" placeholder="Enter name" :value="$store.discount.updateData.name">
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Type</span>
                        <select class="styled-input" name="type">
                            <option value="" selected disabled>Select a type</option>
                            <option value="flat" :selected="$store.discount.updateData?.type === 'flat'">flat</option>
                            <option value="percentage" :selected="$store.discount.updateData?.type === 'percentage'">percentage</option>
                        </select>
                        <p class="text-xs text-yellow-500">note: flat - subtract the discount amount to the total order amount, discount - get the percentage of the total order amount base on the discount amount</p>
                    </label>
                    <label class="block mb-4 text-sm">
                        <span class="text-gray-700">Amount</span>
                        <input class="styled-input" name="amount" type="number" min="0" step="0.01" placeholder="Enter amount" :value="$store.discount.updateData.amount">
                    </label>
                    <div class="form-check">
                        <input name="active" class="float-left w-4 h-4 mt-1 mr-2 align-top transition duration-200 bg-white bg-center bg-no-repeat bg-contain border border-gray-300 rounded-sm appearance-none cursor-pointer form-check-input checked:bg-blue-600 checked:border-blue-600 focus:outline-none" type="checkbox" id="flexCheckChecked" :checked="$store.discount.updateData.active">
                        <label class="inline-block text-gray-800 form-check-label" for="flexCheckChecked">
                            Is Active
                        </label>
                    </div>
                </form>
            </div>
            <div
                class="flex flex-wrap items-center justify-end flex-shrink-0 p-4 border-t border-gray-200 modal-footer rounded-b-md"
                >
                <button
                    type="button"
                    class="inline-block px-6 py-2.5 bg-gray-200 text-gray-700 font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-300 hover:shadow-lg focus:bg-gray-300 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-400 active:shadow-lg transition duration-150 ease-in-out"
                    data-bs-dismiss="modal"
                    >
                    Close
                </button>
                <button
                    form="update-discount-form"
                    type="submit"
                    class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out ml-1"
                    >
                Update
            </button>
            </div>
        </div>
    </div>
</div>
