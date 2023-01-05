<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('inventory', {
                    deleteInventoryData: [],
                    updateInventoryData: [],
                })
            })
        </script>
    </x-slot>

    <x-slot name="header">
        {{ __('Inventory - Import') }}
    </x-slot>

    @include('components.alert-message')

    <div class="inline-flex w-full mt-2 mb-4 overflow-hidden bg-white rounded-lg shadow-md">
        <div class="flex items-center justify-center w-12 bg-yellow-400">
            <i class="text-lg text-white fa-solid fa-circle-exclamation"></i>
        </div>

        <div class="px-4 py-2 -mx-3">
            <div class="mx-3">
                <span class="font-semibold text-yellow-400">Warning</span>
                <p class="text-sm text-gray-600">Items in warehouse will <b>NOT APPEAR</b> in Menu. Transfer items to desired Branch Inventory to be able to add menu items.</p>
            </div>
        </div>
    </div>

    <div class="w-full mb-8">
        <div class="w-full">
            <div class="flex">
                <div class="block p-6 rounded-lg shadow-lg bg-white max-w-sm">
                    <h5 class="text-gray-900 text-xl leading-tight font-medium mb-2">Import</h5>
                    <p class="text-gray-700 text-base mb-4">
                        Make sure to follow proper format of csv before importing data.
                    </p>
                    <form action="{{ route('branch.inventory.import.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input class="form-control
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
                            mb-4
                            focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" type="file" id="formFile" name="file">
                        <button type="submit" class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
