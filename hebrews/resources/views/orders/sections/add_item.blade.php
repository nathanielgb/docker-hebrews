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

    <x-slot name="styles">
        <link
            href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css"
            rel="stylesheet"
        />
        <style>
            .select-menus .optgroup-header {
                font-weight: 700;
                font-style: italic;
                opacity: 1;
                margin: 0 0 0 2px;
            }

            .select-menus .scientific {
                font-weight: normal;
                opacity: 0.6;
                margin: 0 0 0 2px;
            }
            .select-menus .scientific::before {
                content: '(';
            }
            .select-menus .scientific::after {
                content: ')';
            }
        </style>
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
                    <p class="text-sm text-gray-600">You can only add items base on the branch of the order. Adding items when the order is <b>CONFIRMED</b> will automatically deduct quantity of order to the inventory.</p>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white rounded-lg shadow-xs">
            <livewire:add-order-item :order="$order">
        </div>
    </div>

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

        <script type="text/javascript">
            var menus = @json($menus);
            var newCategories = [];

            var newMenus = menus.map(element => {
                let category = {};
                category.value = element.category_id;
                category.label = element?.category?.name;
                category.from = element?.category?.from;
                newCategories.push(category);

                return {
                    class: element.category_id,
                    category: element?.category?.name,
                    value: element.id,
                    name: element.name
                };
            });


            new TomSelect('#select-menu',{
                sortField: [{
                    field: "category",
                    direction: "asc",
                },{
                    field: "name",
                    direction: "asc",
                }],
                options: newMenus,
                optgroups: newCategories,
                optgroupField: 'class',
                labelField: 'name',
                searchField: ['name'],
                render: {
                    optgroup_header: function(data, escape) {
                        return '<div class="optgroup-header">' + escape(data.label) + ' <span class="scientific">' + escape(data.from) + '</span></div>';
                    }
                }
            });

            $('#select-menu').on("click", function() {
                var item = $(this).find(":selected").data("item");
                if (item != undefined) {
                    Alpine.store('order').item = item;
                }
            });
        </script>
    </x-slot>

</x-app-layout>
