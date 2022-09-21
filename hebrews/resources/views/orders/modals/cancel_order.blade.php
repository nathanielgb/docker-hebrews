<div
    class="fixed top-0 left-0 hidden w-full h-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 outline-none modal fade"
    id="cancelOrderModal"
    tabindex="-1"
    aria-labelledby="cancelOrderModalTitle"
    aria-modal="true"
    role="dialog"
    >
    <div class="relative w-auto pointer-events-none modal-dialog modal-dialog-centered">
        <div class="relative flex flex-col w-full text-current bg-white border-none rounded-md shadow-lg outline-none pointer-events-auto modal-content bg-clip-padding">
        <div class="flex items-center justify-between flex-shrink-0 p-4 border-b border-gray-200 modal-header rounded-t-md">
            <h5 class="text-xl font-medium leading-normal text-gray-800" id="exampleModalScrollableLabel">
                Cancel
            </h5>
            <button type="button"
                class="box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 btn-close focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="relative p-4 modal-body">
            <div>
                <p class="text-center text-tiny">Are you sure you want to cancel/void the order?</p>
                <br>
            </div>
            <form id="cancel-order-form" method="post">
                @csrf
                <label for="exampleFormControlTextarea1" class="inline-block mb-2 text-gray-700 form-label"
                >Reason *</label>
                <textarea
                class="
                    form-control
                    block
                    w-full
                    px-3
                    py-1.5
                    text-base
                    font-normal
                    text-gray-700
                    bg-white bg-clip-padding
                    border border-solid border-gray-300
                    rounded
                    transition
                    ease-in-out
                    m-0
                    focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none
                "
                rows="3"
                name="reason"
                placeholder="Your reason of cancellation" required></textarea>
            </form>
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
                form="cancel-order-form"
                type="submit"
                class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                >
                Confirm
            </button>
        </div>
        </div>
    </div>
</div>
