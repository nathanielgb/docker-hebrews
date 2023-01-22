<x-app-layout>
    <x-slot name="headerscript">
 
    </x-slot>

    <x-slot name="header">
        {{ __('Menu - Import') }}
    </x-slot>

    @include('components.alert-message')

    <div class="inline-flex w-full mt-2 mb-4 mr-5 overflow-hidden bg-white rounded-lg shadow-md">
        <div class="flex items-center justify-center w-12" style="background-color: #06b6d4;">
            <i class="text-lg text-white fa-solid fa-note-sticky"></i>
        </div>

        <div class="px-4 py-2 -mx-3">
            <div class="mx-3">
                <span class="font-semibold" style="color: #06b6d4;">Note</span>
                <ol class="list-decimal list-inside">
                    <li clas="text-sm text-gray-600">Adding items from the imported csv/xlsx file that is already in the database will we skipped/ignored.</li>
                    <li clas="text-sm text-gray-600">Please follow the proper format of headings as seen in the import sample guide.</li>
                    <li clas="text-sm text-gray-600">Items with <b>Failed</b> status will not be saved while status with <b>Success</b> will be saved.</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="w-full mb-8">
        <div class="w-full">
            <div class="flex flex-col">
                <div class="block p-6 rounded-lg shadow-lg bg-white max-w-sm mr-5 mb-5">
                    <h5 class="text-gray-900 text-xl leading-tight font-medium mb-2">Import</h5>
                    <p class="text-gray-700 text-base mb-4">
                        Make sure to follow proper format of csv/xlsx before importing data.
                    </p>
                    <form action="{{ route('menu.import') }}" method="post" enctype="multipart/form-data">
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

                <div class="block p-6 rounded-lg shadow-lg bg-white mr-5 mmd:max-w-2xl mxl:max-w-3xl">
                    @if(session()->has('records'))
                        <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
                            <div class="w-full overflow-x-auto">
                                <table class="w-full whitespace-no-wrap">
                                    <thead>
                                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                        <th class="px-4 py-3">Row #</th>
                                        <th class="px-4 py-3">Code</th>
                                        <th class="px-4 py-3">Name</th>
                                        <th class="px-4 py-3">Category</th>
                                        <th class="px-4 py-3">Sub-Category</th>
                                        <th class="px-4 py-3">Branch ID</th>
                                        <th class="px-4 py-3">Inventory Code</th>
                                        <th class="px-4 py-3">Units</th>
                                        <th class="px-4 py-3">Regular Price</th>
                                        <th class="px-4 py-3">Retail Price</th>
                                        <th class="px-4 py-3">Wholesale Price</th>
                                        <th class="px-4 py-3">Distributor Price</th>
                                        <th class="px-4 py-3">Rebranding Price</th>
                                        <th class="px-4 py-3 text-center">Is Beans</th>
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
                                                    {{ $record['code'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['name'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['category'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['sub_category'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['branch_id'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['inventory_code'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['units'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['regular_price'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['retail_price'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['wholesale_price'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['distributor_price'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $record['rebranding_price'] }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    @if ($record['is_beans'])
                                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-green-600 rounded-full leading-sm">
                                                            YES
                                                        </div>
                                                    @else
                                                        <div class="inline-flex items-center px-3 py-1 text-xs font-bold text-white uppercase bg-red-600 rounded-full leading-sm">
                                                            NO
                                                        </div>
                                                    @endif
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
                                                <td colspan="17" class="px-4 py-3 text-sm text-center">
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
                                                name
                                            </th>
                                            <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                                code
                                            </th>
                                            <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                                category
                                            </th>
                                            <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                                sub_category
                                            </th>
                                            <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                                branch_id
                                            </th>
                                            <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                                inventory_code
                                            </th>
                                            <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                                units
                                            </th>
                                            <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                                regular_price
                                            </th>
                                            <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                                retail_price
                                            </th>
                                            <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                                rebranding_price
                                            </th>
                                            <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                                wholesale_price
                                            </th>
                                            <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                                distributor_price
                                            </th>
                                            <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4 border-r">
                                                is_beans
                                            </th>
                                            <th scope="col" class="text-sm font-semi-bold text-gray-900 px-6 py-4">
                                                action
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-b">
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                                    specify name of menu (must be unique)
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                                    specify unique code for menu
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                                    specify category name (must exist in categories table)
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                                    specify sub-category name (must exist in categories table)
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                                    specify which branch id does the inventory code belong
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                                    specify inventory code of the menu
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                                    specify number of units for the menu per inventory stock
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                                    specify regular price
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                                    specify retail price
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                                    specify rebranding price
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                                    specify wholesale price
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                                    specify distributor price
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-r break-all">
                                                    specify if the item is beans
                                                </td>
                                                <td class="text-sm text-gray-900 font-normal px-6 py-4 whitespace-nowrap border-rbreak-all">
                                                    action to perform (i.e. A or U)
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Name</span> - this field is <b><em>required</em></b>, should have a maximum of <b><em>255</em></b> characters and should be <b><em>unique</em></b>.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Code</span> - this field is <b><em>required</em></b>, may have lowercase alpha-numeric characters, as well as dashes and underscores, and should be <b><em>unique</em></b>.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Category</span> - this field is <b><em>required</em></b>, should have a maximum of 255 characters and should exist in Categories table.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Sub-Category</span> - this field is <b><em>required</em></b>, should have a maximum of 255 characters and should exist in Categories table.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Branch ID</span> - this field is <b><em>required</em></b>, and should be <b><em>numeric</em></b>. Specify the branch ID in which the inventory item belongs to.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Inventory Code</span> - this field <b><em>can be empty</em></b> and may have lowercase alpha-numeric characters, as well as dashes and underscores. Specify the inventory code that is linked to the menu item <b><em>(inventory should exist in branch)</em></b>.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Units</span> - this field is <b><em>required</em></b> and should be <b><em>numeric</em></b>. Specify number of units of menu item per inventory item.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Regular Price</span> - this field is <b><em>can be empty</em></b> and should be <b><em>numeric</em></b>.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Retail Price</span> - this field is <b><em>can be empty</em></b> and should be <b><em>numeric</em></b>.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Rebranding Price</span> - this field is <b><em>can be empty</em></b> and should be <b><em>numeric</em></b>.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Wholesale Price</span> - this field is <b><em>can be empty</em></b> and should be <b><em>numeric</em></b>.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Distributor Price</span> - this field is <b><em>can be empty</em></b> and should be <b><em>numeric</em></b>.
                        </div>
                        <div class="py-2 px-6 mb-4 text-base text-blue-700 mb-3" role="alert">
                            <span class="font-bold text-blue-800">Is Beans</span> - this field is <b><em>required</em></b> and should be <b><em>boolean (i.e. 1 or 0)</em></b>.
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
