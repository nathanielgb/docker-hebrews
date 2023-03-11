<div
    class="fixed top-0 left-0 hidden w-full h-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 outline-none modal fade"
    id="exportModal"
    tabindex="-1"
    aria-labelledby="exportModalTitle"
    aria-modal="true"
    role="dialog"
    >
    <div class="relative w-auto pointer-events-none modal-dialog modal-dialog-centered">
        <div class="relative flex flex-col w-full text-current bg-white border-none rounded-md shadow-lg outline-none pointer-events-auto modal-content bg-clip-padding">
        <div class="flex items-center justify-between flex-shrink-0 p-4 border-b border-gray-200 modal-header rounded-t-md">
            <h5 class="text-xl font-medium leading-normal text-gray-800" id="exampleModalScrollableLabel">
                Export
            </h5>
            <button type="button"
                class="box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 btn-close focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="relative p-4 modal-body">
            <div class="text-center">
                Are you sure you want to export the order report with this filters?
                <form id="exportForm" action="{{ route('orders.report.export', request()->all()) }}" method="GET" target="_blank">
                    <ul>
                        @if (request()->date)
                            <li>Date: <b>{{ request()->date }}</b></li>
                            <input type="hidden" name="date" value="{{ request()->date }}">
                        @endif
                        @if (request()->order_id)
                            <li>Order/s: <b>{{ request()->order_id }}</b></li>
                            <input type="hidden" name="order_id" value="{{ request()->order_id }}">
                        @endif
                        @if (request()->status)
                            <li>Status: <b>{{ request()->status }}</b></li>
                            <input type="hidden" name="status" value="{{ request()->status }}">
                        @endif
                        @if (request()->branch_id)
                            <li>Branch ID: <b>{{ request()->branch_id }}</b></li>
                            <input type="hidden" name="branch_id" value="{{ request()->branch_id }}">
                        @endif
                        @if (request()->servername)
                            <li>Admin: <b>{{ request()->servername }}</b></li>
                            <input type="hidden" name="servername" value="{{ request()->servername }}">
                        @endif
                        @if (request()->customer_name)
                            <li>Customer Name: <b>{{ request()->customer_name }}</b></li>
                            <input type="hidden" name="customer_name" value="{{ request()->customer_name }}">
                        @endif
                    </ul>
                </form>
            </div>
        </div>
        <div
            class="flex flex-wrap items-center justify-end flex-shrink-0 p-4 border-t border-gray-200 modal-footer rounded-b-md"
            >
            <button
                type="button"
                class="mr-1 inline-block px-6 py-2.5 bg-gray-200 text-gray-700 font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-300 hover:shadow-lg focus:bg-gray-300 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-400 active:shadow-lg transition duration-150 ease-in-out"
                data-bs-dismiss="modal"
                >
                Close
            </button>
            <button
                form="exportForm"
                type="submit"
                class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out ml-1"
                >
                Confirm
            </button>
        </div>
        </div>
    </div>
</div>
