{{-- <div class="flex justify-center space-x-1">
    <button
    @click="dineIn=true; takeOut=false"
        :class = "{ 'bg-purple-600 text-white' : dineIn  }"
        class="px-5 py-3 font-medium leading-5 transition-colors duration-150 border border-transparent rounded-lg active:bg-purple-600 focus:outline-none focus:shadow-outline-purple"
    >
        Dine-in
    </button>
    <button
    @click="dineIn=false; takeOut=true"
        :class = "{ 'bg-purple-600 text-white' : takeOut  }"
        class="px-5 py-3 font-medium leading-5 transition-colors duration-150 bg-white border border-transparent rounded-lg active:bg-purple-600 focus:outline-none focus:shadow-outline-purple"
    >
        Take-out
    </button>
</div> --}}
<div x-data="{ active: 0, items: {{ $categories }} }" class="space-y-4">
    <template x-for="{ id, name, from, menus} in items" :key="id">
        <div x-data="{
            get expanded() {
                return this.active === this.id
            },
            set expanded(value) {
                this.active = value ? this.id : null
            },
        }" role="region" class="border border-gray-300 rounded-md">
            <h2>
                <button
                    @click="expanded =!expanded"
                    :aria-expanded="expanded"
                    class="flex items-center justify-between w-full px-6 py-3 text-xl item-center"
                >
                    <span class="font-mono text-gray-600" x-text="name"></span>
                    <span x-show="expanded" aria-hidden="true" class="ml-4"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#4b5563" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 mr-2 -ml-1 feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></span>
                    <span x-show="!expanded" aria-hidden="true" class="ml-4"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#4b5563" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 mr-2 -ml-1 feather feather-chevron-up"><polyline points="18 15 12 9 6 15"></polyline></svg></span>
                </button>
            </h2>
            <div x-show="expanded" class="flex flex-col px-6 pb-4 space-y-4">
                <template x-for="item in menus">
                    <button
                        @click="addOrderItem(item, from)"
                        class="p-4 bg-gray-100 rounded-lg shadow-xs"
                    >
                        <span x-text="item.name"></span>
                    </button>
                </template>
            </div>
        </div>
    </template>
</div>
