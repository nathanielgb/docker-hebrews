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
                    <li clas="text-sm text-gray-600">Adding items from the imported csv/xlxs file that is already in the database will we skipped/ignored.</li>
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
                        Make sure to follow proper format of csv/xlxs before importing data.
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

                <div class="block p-6 rounded-lg shadow-lg bg-white overflow-hidden">
                    @if(session()->has('records'))
                    <div class="w-full mb-8 overflow-hidden border rounded-lg shadow-xs">
                        <div class="w-full overflow-x-auto">
                            <table class="w-full whitespace-no-wrap">
                                <thead>
                                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                    <th class="px-4 py-3">Row Number</th>
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
                                                    <span class="font-semibold text-green-600">Success</span>
                                                @else
                                                    <span class="font-semibold text-red-600">Failed</span>
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
                                                No records found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
