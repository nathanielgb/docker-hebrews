<x-app-layout>
    <x-slot name="header">
        {{ __('Dispatch Orders') }}
    </x-slot>

    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('data', {
                    orderId: null,
                })
            })
        </script>
    </x-slot>
        @include('components.alert-message')
        <livewire:dispatch-dashboard :orders="$orders" />

        @include('dispatch.modals.serve')
        @include('dispatch.modals.clear')

</x-app-layout>
