<x-app-layout>
    <x-slot name="headerscript">
        <!-- You need focus-trap.js to make the modal accessible -->
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('order', {
                    item: [],
                })
            })
        </script>

    </x-slot>

    <div class="container grid mx-auto space-y-2" style="max-width: 850px;">
        <h2 class="my-3 text-2xl font-semibold text-gray-700">Add Order Item</h2>
        @include('components.alert-message')

        <div class="inline-flex w-full mt-2 mb-4 overflow-hidden bg-white rounded-lg shadow-md">
            <div class="flex items-center justify-center w-12 bg-yellow-400">
                <i class="text-lg text-white fa-solid fa-circle-exclamation"></i>
            </div>

            <div class="px-4 py-2 -mx-3">
                <div class="mx-3">
                    <span class="font-semibold text-yellow-400">Warning</span>
                    <p class="text-sm text-gray-600">You can only add items base on the branch of the order.</p>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white rounded-lg shadow-xs">
            <livewire:add-order-item :order="$order">
        </div>
    </div>

    <x-slot name="scripts">
        <script type="text/javascript">
            $('#item-select').on("click", function() {
                var item = $(this).find(":selected").data("item");
                if (item != undefined) {
                    Alpine.store('order').item = item;
                }
            });
        </script>
    </x-slot>

</x-app-layout>
