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
        <div class="flex items-center justify-center w-12" style="background-color: #06b6d4;">
            <i class="text-lg text-white fa-solid fa-note-sticky"></i>
        </div>

        <div class="px-4 py-2 -mx-3">
            <div class="mx-3">
                <span class="font-semibold" style="color: #06b6d4;">Note</span>
                <ol class="list-decimal list-inside">
                    <li clas="text-sm text-gray-600">Adding items from the imported csv/xlsx file that is already in the database will we skipped/ignored.</li>
                    <li clas="text-sm text-gray-600">Name, unit, and inventory code <b>WILL NOT</b> changed when updating. You can only change number of stock when updating.</li>
                    <li clas="text-sm text-gray-600">Please follow the proper format of headings as seen in the import sample guide.</li>
                    <li clas="text-sm text-gray-600">Items with <b>Failed</b> status will not be saved while status with <b>Success</b> will be saved.</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="w-full mb-8">
        <div class="w-full">
            <div class="flex">
                <div class="block p-6 rounded-lg shadow-lg bg-white max-w-sm mr-5">
                    <h5 class="text-gray-900 text-xl leading-tight font-medium mb-2">Import</h5>
                    <p class="text-gray-700 text-base mb-4">
                        Make sure to follow proper format of csv/xlsx before importing data.
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

                <div class="block p-6 rounded-lg shadow-lg bg-white overflow-hidden mmd:max-w-2xl mxl:max-w-3xl">
                    @if(session()->has('records'))
                        <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
                            <div class="w-full overflow-x-auto">
                                <table class="w-full whitespace-no-wrap">
                                    <thead>
                                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                        <th class="px-4 py-3">Row #</th>
                                        <th class="px-4 py-3">Category Id</th>
                                        <th class="px-4 py-3">Branch Id</th>
                                        <th class="px-4 py-3">Inventory Code</th>
                                        <th class="px-4 py-3">Name</th>
                                        <th class="px-4 py-3">Unit</th>
                                        <th class="px-4 py-3">Stock</th>
                                        <th class="px-4 py-3 text-center">Action</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-center">Errors</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y">
                                        @forelse (session('records') as $record)
                                            <tr class="text-gray-700">
                                                <td class="px-4 py-3 text-sm text-s">
                                                    {{ $record['row_number'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-s">
                                                    {{ $record['category_id'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-s">
                                                    {{ $record['branch_id'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['inventory_code'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['name'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['unit'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['stock'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    {{ $record['action'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    @if ($record['status'] == 'success')
                                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-green-600 rounded-full leading-sm">
                                                            SUCCESS
                                                        </div>
                                                    @else
                                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                                            FAILED
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <ol class="list-decimal list-inside">
                                                        @foreach($record['errors'] ?? [] as $column => $errors)
                                                            @foreach($errors as $error)
                                                                <li class="text-red-600">{{ $error }}</li>
                                                            @endforeach
                                                        @endforeach
                                                    </ol>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="text-gray-700">
                                                <td colspan="9" class="px-4 py-3 text-sm text-center">
                                                    No records to import.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col">
                            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="overflow-hidden">
                                <table class="min-w-full border text-center">
                                    <thead class="border-b">
                                    <tr>
                                        <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                        inventory_code
                                        </th>
                                        <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                        branch_id
                                        </th>
                                        <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                        name
                                        </th>
                                        <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                        category_id
                                        </th>
                                        <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                        unit
                                        </th>
                                        <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                        stock
                                        </th>
                                        <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4">
                                        action
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="border-b">
                                        <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                            the unique identifier for the inventory item
                                        </td>
                                        <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                            specify branch of the item <br>(enter 'w' to save in warehouse)
                                        </td>
                                        <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                            specify name (for adding)
                                        </td>
                                        <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                            specify category ID
                                        </td>
                                        <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                            specify unit (i.e. Kg, g, pcs., boxes) (for adding)
                                        </td>
                                        <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                            specify running stock of the item
                                        </td>
                                        <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-rbreak-all">
                                            action to perform (i.e. A or U)
                                        </td>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Inventory Code</span> - this field is <b><em>required</em></b> and may have lowercase alpha-numeric characters, as well as dashes and underscores.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Category ID</span> - this field is <b><em>required</em></b> and should exist in  Inventory Categories table.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Branch ID</span> - this field is <b><em>required</em></b>, <b><em>numeric</em></b> and should <b><em>exist in the branches</em></b> table in user section. <br> Branch ID with a value of <b>w</b> will be saved in the Warehouse section otherwise it will be save in the inventory of the branch specified.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Name</span> - this field is <b><em>required</em></b> and should have a maximum of 255 characters. (will not change when updating)
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Unit</span> - this field is <b><em>required</em></b> and should be one of the following <b><em>Kg, g, pcs, boxes</em></b>. (will not change when updating)
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Stock</span> - this field is <b><em>required</em></b> and should have a minimum value of <b><em>0</em></b>.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Action</span> - this field is <b><em>required</em></b>, should have a value of <b><em>A</em></b> when you want to add the item and a value of <b><em>U</em></b> if you want to update the stock of an existing item.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
