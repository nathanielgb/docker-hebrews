<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script src="{{ asset('js/focus-trap.js') }}"></script>
    </x-slot>

    <x-slot name="styles">
        <link
            href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css"
            rel="stylesheet"
        />
    </x-slot>

    <x-slot name="header">
        {{ __('Take Order') }}
    </x-slot>


    <div class="container grid px-6 mx-auto space-y-2">
        @include('components.alert-message')

        <div class="grid grid-cols-2 gap-5">
            <div class="p-4 bg-white rounded-lg shadow-xs">
                @include('orders.sections.select_menu')
            </div>
            <div class="p-4 bg-white rounded-lg shadow-xs">
                @include('orders.sections.order_overview')
            </div>
        </div>
    </div>
    @include('orders.modals.confirm')
    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script type="text/javascript">
            new TomSelect('#select-table', {
                plugins: ['remove_button'],
            });

            $("#dinein-toggle").click(function(){
                // Activate styles for dineine
                $("#dinein-toggle").removeClass("bg-white text-purple-600 text-purple-600  border-purple-600");
                $("#dinein-toggle").addClass("bg-purple-600 text-white");

                // Deactivate styles for takeout
                $("#takeout-toggle").removeClass("bg-purple-600 text-white");
                $("#takeout-toggle").addClass("bg-white text-purple-600 text-purple-600  border-purple-600");
            });


            $("#takeout-toggle").click(function(){
                // Activate styles for takeout
                $("#takeout-toggle").removeClass("bg-white text-purple-600 text-purple-600  border-purple-600");
                $("#takeout-toggle").addClass("bg-purple-600 text-white");

                // Deactivate styles for dinein
                $("#dinein-toggle").removeClass("bg-purple-600 text-white");
                $("#dinein-toggle").addClass("bg-white text-purple-600 text-purple-600  border-purple-600");
            });
        </script>
    </x-slot>
</x-app-layout>
