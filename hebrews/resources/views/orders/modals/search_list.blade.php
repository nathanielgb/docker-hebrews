<div
    class="fixed top-0 left-0 hidden w-full h-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 outline-none modal fade"
    id="searchModal"
    tabindex="-1"
    aria-labelledby="searchModalTitle"
    aria-modal="true"
    role="dialog"
    >
    <div class="relative w-auto pointer-events-none modal-dialog modal-dialog-centered">
        <div class="relative flex flex-col w-full text-current bg-white border-none rounded-md shadow-lg outline-none pointer-events-auto modal-content bg-clip-padding">
        <div class="flex items-center justify-between flex-shrink-0 p-4 border-b border-gray-200 modal-header rounded-t-md">
            <h5 class="text-xl font-medium leading-normal text-gray-800" id="exampleModalScrollableLabel">
                Search
            </h5>
            <button type="button"
                class="box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 btn-close focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="relative p-4 modal-body">
            <form id="search-order-form" action="{{ route('order.list') }}" method="get" autocomplete="off">
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Order Number</span>
                    <input class="styled-input" name="order_id" type="text" placeholder="Enter order number">
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Branch Id</span>
                    <input class="styled-input" name="branch_id" type="text" placeholder="Enter branch id">
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Customer Name</span>
                    <input class="styled-input" name="cust_name" type="text" placeholder="Enter name">
                </label>
                <label class="block mb-4 text-sm">
                    <span class="mb-2 text-gray-700 dark:text-gray-400">Status</span>
                    <div class="flex">
                        <div class="form-check form-check-inline">
                            <input class="float-left w-4 h-4 mt-1 mr-2 align-top transition duration-200 bg-white bg-center bg-no-repeat bg-contain border border-gray-300 rounded-full appearance-none cursor-pointer form-check-input checked:bg-blue-600 checked:border-blue-600 focus:outline-none" type="radio" name="status" value="pending">
                            <label class="inline-block text-gray-800 form-check-label" for="inlineRadio10">Pending</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="float-left w-4 h-4 mt-1 mr-2 align-top transition duration-200 bg-white bg-center bg-no-repeat bg-contain border border-gray-300 rounded-full appearance-none cursor-pointer form-check-input checked:bg-blue-600 checked:border-blue-600 focus:outline-none" type="radio" name="status" value="confirmed">
                            <label class="inline-block text-gray-800 form-check-label" for="inlineRadio10">Confirmed</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="float-left w-4 h-4 mt-1 mr-2 align-top transition duration-200 bg-white bg-center bg-no-repeat bg-contain border border-gray-300 rounded-full appearance-none cursor-pointer form-check-input checked:bg-blue-600 checked:border-blue-600 focus:outline-none" type="radio" name="status" value="completed">
                            <label class="inline-block text-gray-800 form-check-label" for="inlineRadio20">Completed</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="float-left w-4 h-4 mt-1 mr-2 align-top transition duration-200 bg-white bg-center bg-no-repeat bg-contain border border-gray-300 rounded-full appearance-none cursor-pointer form-check-input checked:bg-blue-600 checked:border-blue-600 focus:outline-none" type="radio" name="status" value="cancelled">
                            <label class="inline-block text-gray-800 form-check-label" for="inlineRadio20">Cancelled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="float-left w-4 h-4 mt-1 mr-2 align-top transition duration-200 bg-white bg-center bg-no-repeat bg-contain border border-gray-300 rounded-full appearance-none cursor-pointer form-check-input checked:bg-blue-600 checked:border-blue-600 focus:outline-none" type="radio" name="status" value="all" checked >
                            <label class="inline-block text-gray-800 form-check-label" for="inlineRadio20">All</label>
                        </div>
                    </div>
                </label>

                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Date</span>
                    <input name="date" class="styled-input" type="text" placeholder="Enter date range">
                </label>
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
                form="search-order-form"
                type="submit"
                class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out ml-1">
                Search
            </button>
        </div>
        </div>
    </div>
</div>

