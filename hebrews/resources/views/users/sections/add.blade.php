<x-app-layout>

    <x-slot name="header">
        {{ __('Add User') }}
    </x-slot>

    <div class="container grid px-6 mx-auto space-y-2">
        @include('components.alert-message')

        <div class="p-4 bg-white rounded-lg shadow-xs">
            <form id="add-user-form" action="{{ route('users.add') }}" method="post" autocomplete="off">
                @csrf
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Admin Type</span>
                    <select class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:outline-none focus:shadow-outline-gray dark:focus:shadow-outline-gray" name="type">
                        <option value="" selected disabled>Select Admin Type</option>
                        @foreach ($admin_types as $type)
                            <option value="{{ $type->name }}" >{{ $type->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Name</span>
                    <input class="styled-input" name="name" type="text" placeholder="Enter name" required>
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Username</span>
                    <input class="styled-input" name="username" type="text" placeholder="enter username" required>
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Password</span>
                    <input class="styled-input" name="password" type="password"  placeholder="Enter password" required>
                </label>
                <label class="block mb-4 text-sm">
                    <span class="text-gray-700">Confirm Password</span>
                    <input class="styled-input" name="password_confirmation" type="password"  placeholder="Confirm password" required>
                </label>
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('users.index') }}" class="px-10 py-4 font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
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
