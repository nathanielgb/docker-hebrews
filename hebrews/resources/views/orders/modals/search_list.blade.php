<!-- Modal backdrop. This what you want to place close to the closing body tag -->
<div
  x-show="isSearchModalOpen"
  x-transition:enter="transition ease-out duration-150"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-150"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  class="fixed inset-0 z-30 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
>
  <!-- Modal -->
  <div
    x-show="isSearchModalOpen"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0 transform translate-y-1/2"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0  transform translate-y-1/2"
    @click.away="closeModal"
    @keydown.escape="closeModal"
    class="w-full px-6 py-4 overflow-hidden bg-white rounded-t-lg dark:bg-gray-800 sm:rounded-lg sm:m-4 sm:max-w-xl"
    role="dialog"
    id="search-modal"
  >
    <!-- Remove header if you don't want a close icon. Use modal body to place modal tile. -->
    <header class="flex justify-end">
      <button
        class="inline-flex items-center justify-center w-6 h-6 text-gray-400 transition-colors duration-150 rounded dark:hover:text-gray-200 hover: hover:text-gray-700"
        aria-label="close"
        @click="closeModal"
      >
        <svg
          class="w-4 h-4"
          fill="currentColor"
          viewBox="0 0 20 20"
          role="img"
          aria-hidden="true"
        >
          <path
            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
            clip-rule="evenodd"
            fill-rule="evenodd"
          ></path>
        </svg>
      </button>
    </header>
    <!-- Modal body -->
    <div class="mt-4 mb-6">
      <!-- Modal title -->
      <p
        class="mb-2 text-lg font-semibold text-gray-700 dark:text-gray-300"
      >
        Search
      </p>

      <form id="search-orders-form" action="{{ route('order.list') }}" method="get" autocomplete="off">
        <label class="block mb-4 text-sm">
          <span class="text-gray-700 dark:text-gray-400">Order ID</span>
          <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" name="id" type="text" placeholder="Enter Order ID">
        </label>
        <label class="block mb-4 text-sm">
          <span class="text-gray-700 dark:text-gray-400">Table/s</span>
          <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" name="table" type="text" placeholder="Enter table numbers">
          <p class="text-xs italic text-gray-400">Enter multiple table numbers by separating them using comma (,) i.e. (1,2,3,4,5)</p>
        </label>
      </form>


    </div>
    <footer
      class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50 dark:bg-gray-800"
    >
      <button
        @click="closeModal"
        class="w-full px-5 py-3 text-sm font-medium leading-5 text-black text-gray-700 transition-colors duration-150 border border-gray-300 rounded-lg dark:text-gray-400 sm:px-4 sm:py-2 sm:w-auto active:bg-transparent hover:border-gray-500 focus:border-gray-500 active:text-gray-500 focus:outline-none focus:shadow-outline-gray"
      >
        Cancel
      </button>
      <button
        class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg sm:w-auto sm:px-4 sm:py-2 active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
        type="submit"
        form="search-menu-form"
      >
        Search
      </button>
    </footer>
  </div>
</div>
<!-- End of modal backdrop -->

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
                    <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-green-400 focus:outline-none focus:shadow-outline-green dark:text-gray-300 dark:focus:shadow-outline-gray form-input" name="order_id" type="text" placeholder="Enter order number">
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Customer Name</span>
                    <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-green-400 focus:outline-none focus:shadow-outline-green dark:text-gray-300 dark:focus:shadow-outline-gray form-input" name="cust_name" type="text" placeholder="Enter name">
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
                    <input name="date" class="block w-full mt-1 text-sm focus:border-green-400 focus:outline-none focus:shadow-outline-green form-input" type="text" placeholder="Enter date range">
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

