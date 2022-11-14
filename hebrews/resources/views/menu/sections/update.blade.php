<x-app-layout>

    <x-slot name="header">
        {{ __('Update Menu') }}
    </x-slot>

    <div class="container grid px-6 mx-auto space-y-2">
        @include('components.alert-message')

        <div class="p-4 bg-white rounded-lg shadow-xs">
            <form id="update-menu-form" action="{{ route('menu.update',['menu_id'=>$item->id]) }}" method="post">
                @csrf
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Item ID</span>
                    <input class="block w-full mt-1 text-sm bg-gray-100 border focus:outline-none focus:shadow-outline-gray form-input" disabled readonly value="{{ $item->id }}">
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Name</span>
                    <input class="styled-input" name="menu" type="text" placeholder="Cheeseburger" value="{{ $item->name }}">
                </label>

                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Category</span>
                    <select class="block w-full mt-1 text-sm form-select focus:outline-none focus:shadow-outline-gray" name="category">
                        <option value="" disabled>Select a category</option>
                        {{-- @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @if($category->id == $item->category_id) selected @endif>{{ $category->name }}</option>
                        @endforeach --}}
                    </select>
                </label>


                <div class="flex justify-center space-x-4">
                    <a href="{{ route('menu.index') }}" class="px-10 py-4 font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                        <span>BACK</span>
                    </a>
                    <button class="px-10 py-4 font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                        SAVE
                    </button>
                </div>

            </form>
        </div>
    </div>

</x-app-layout>
