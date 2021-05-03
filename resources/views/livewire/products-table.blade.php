<div>
    <div>
        <header class="border-b border-solid border-gray-200 p-4 text-lg font-medium pb-14">

            <div class="relative text-sm w-60 float-left">
                <label>
                    <input wire:model="search" type="text" class="border-b border-solid border-gray-200 h-10 w-60 pr-8 pl-5 rounded z-0 focus:shadow focus:outline-none" placeholder="Search products...">
                </label>
                <div class="absolute top-2 right-5"> <i class="fa fa-search text-gray-400 z-20 hover:text-gray-500"></i> </div>
            </div>

            <button wire:click="addProduct" class="float-right bg-green-400 text-white py-2 px-4 pr-6 text-sm rounded hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green">
                <svg class="inline-block w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add product
            </button>

            <div class="float-right mr-2">
                <x-button.secondary wire:click="$toggle('showModal')" class="flex items-center space-x-2"><x-icon.upload class="text-cool-gray-500"/> <span>Import</span></x-button.secondary>
            </div>

            <div class="float-right mr-2">
                <x-dropdown label="Bulk Actions">
                    <x-dropdown.item type="button" wire:click="exportExcelSelectate" class="flex items-center space-x-2">
                        <x-icon.download class="text-cool-gray-400"/> <span>Export (.xlsx)</span>
                    </x-dropdown.item>

                    @if($selected)
                    <x-dropdown.item type="button" wire:click="$toggle('showDeleteModal')" class="flex items-center space-x-2">
                        <x-icon.trash class="text-cool-gray-400"/> <span>Delete</span>
                    </x-dropdown.item>
                    @else
                     <x-dropdown.item type="button" wire:click="" class="flex items-center space-x-2">
                        <x-icon.trash class="text-cool-gray-400"/> <span>Delete</span>
                    </x-dropdown.item>
                    @endif

                </x-dropdown>
            </div>
        </header>

        @if(count($products) > 0)

            <table class="min-w-max w-full table-auto">
                <thead>
                <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                    <th class="text-left px-2 w-5">
                        <label class="text-teal-500 inline-flex justify-between hover:bg-gray-200 px-2 py-2 rounded-lg cursor-pointer">
                            <input wire:model="selectPage" type="checkbox" class="form-checkbox rowCheckbox focus:outline-none focus:shadow-outline">
                        </label>
                    </th>
                    <x-table.heading sortable direction="asc" wire:click="sortBy('name')" :direction="$sortField === 'denumire' ? $sortDirection : null" class="py-3 px-6 text-left">Product Name</x-table.heading>
                    <x-table.heading sortable direction="asc" wire:click="sortBy('updated_at')" :direction="$sortField === 'updated_at' ? $sortDirection : null" class="py-3 px-6 text-left">Last Update</x-table.heading>
                    <x-table.heading class="py-3 px-6 text-center">Stock</x-table.heading>
                    <x-table.heading class="py-3 px-6 text-center"></x-table.heading>
                </tr>
                </thead>

                @if(count($selected) && !$selectPage)
                    <tr>
                        <td class="bg-cool-gray-200 p-4" wire:key="row-message" colspan="5">
                            <span class="text-xs p4 text-cool-gray-700">You have selected <strong>{{ count($selected) }}</strong> products.</span>
                        </td>
                    </tr>

                @elseif($selectPage)

                <tr>
                    <td class="bg-cool-gray-200 p-4" wire:key="row-message" colspan="5">

                        @unless($selectAll)

                        <span class="text-xs p4 text-cool-gray-700">You have selected <strong>{{ count($selected) }}</strong> products, do you want to select all <strong>{{ $products->total() }}</strong> ?</span>
                        <button wire:click="selectAll" class="text-xs text-blue-600 uppercase underline">Select all</button>

                        @else

                        <span class="text-xs p4 text-cool-gray-700">
                            You have selected <strong>{{ $products->total($selected) }}</strong> products.
                        </span>

                        @endif

                    </td>
                </tr>

                @endif


                <tbody class="text-gray-600 text-xs font-light">

                @foreach($products as $product)

                    <tr wire:key="row-{{ $product->id }}" class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="px-2 float-left">
                            <label class="text-teal-500 inline-flex justify-between items-center hover:bg-gray-200 px-2 py-2 mt-1 rounded-lg cursor-pointer">
                                <input wire:model="selected" value="{{ $product->id }}" type="checkbox" class="form-checkbox rowCheckbox focus:outline-none focus:shadow-outline">
                            </label>
                        </td>

                        <td class="py-3 px-6 text-left whitespace-nowrap">
                            <div class="flex items-center">
                            <span class="font-medium">
                                <a class="uppercase font-bold text-gray-500" href="">{{ $product->name }}</a>
                            </span>
                            </div>
                        </td>

                        <td class="py-3 px-6 text-left">
                            <div class="flex items-center">
                                {{ $product->updated_at }}
                            </div>
                        </td>

                        <td class="py-3 px-6 text-center">
                            <span class="bg-green-200 text-green-600 py-1 px-3 rounded-full text-xs">In stock</span>
                        </td>

                        <td class="py-3 px-6 text-center">
                            <button class="font-medium underline" wire:click="edit({{ $product->id }})">Edit</button>
                        </td>
                    </tr>

                @endforeach

                </tbody>
            </table>

            <div class="py-4 px-4 text-xs">
                {{ $products->withQueryString()->links() }}
            </div>

        @else
            <p class="text-sm p-4">No products found.</p>
        @endif

    <!-- Delete Modal -->

    <form wire:submit.prevent="deleteSelected">
        <x-modal.confirmation wire:model.defer="showDeleteModal">
            <x-slot name="title">Delete</x-slot>

            <x-slot name="content">
                <div class="py-8 text-cool-gray-700 text-sm">
                    Are you sure ? 
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$set('showDeleteModal', false)">Cancel</x-button.secondary>
                <x-button.danger type="submit">Delete</x-button.danger>
            </x-slot>
        </x-modal.confirmation>
    </form>

    <!-- Edit / Save modal -->

    <form wire:submit.prevent="saveProduct">
            <x-modal.dialog wire:model.defer="showEditModal">
                <x-slot name="title">Products</x-slot>

                <x-slot name="content">
                    <x-input.group for="title" label="Name" :error="$errors->first('editProduct.name')">
                        <x-input.text wire:model="editProduct.name" id="title" placeholder="Product Name" />
                    </x-input.group>
                </x-slot>

                <x-slot name="footer">
                    <x-button.secondary wire:click="$set('showEditModal', false)">Cancel</x-button.secondary>

                    <x-button.primary type="submit">Save</x-button.primary>
                </x-slot>
            </x-modal.dialog>
    </form>

</div>

</div>
