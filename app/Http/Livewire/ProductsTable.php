<?php

namespace App\Http\Livewire;

use Maatwebsite\Excel\Facades\Excel;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Products;

class ProductsTable extends Component
{
    use WithPagination;

    public $selectPage = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public Products $editProduct;
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $selectAll = false;
    public $selected = [];
    public $search = '';
    
    protected $queryString = [
        'sortField', 
        'sortDirection',
    ];

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'editProduct.name' => 'required',
    ];

    public function mount() { $this->editProduct = Products::make(['created_at' => now()]); }

    /** Sort Products **/

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    /** Select Products **/

    public function selectAll()
    {
        $this->selectAll = true;
    }

    public function selectPage()
    {
        $this->selected = $this->produse->pluck('id')->map(fn($id) => (string) $id);
    }

    /** CRUD **/

    public function edit(Products $product)
    {
        if ($this->editProduct->isNot($product)) $this->editProduct = $product;
        $this->showEditModal = true;
    }

    public function addProduct()
    {
        if ($this->editProduct->getKey()) $this->editProduct = Products::make(['created_at' => now()]);
        $this->showEditModal = true;
    }

    public function saveProduct()
    {
        $this->validate();
        $this->editProduct->save();
        $this->showEditModal = false;
    }

    public function deleteSelected()
    {
        $products = Products::whereKey($this->selected);
        $products->delete();
        $this->showDeleteModal = false;
        $this->selected = [];
    }

    public function exportSelected() 
    {
       return (new ExportProduse($this->selected))->download('products.xlsx');
    }

    public function getProductsQueryProperty()
    {

        return Products::query()
            ->search('name', $this->search)
            ->orderBy($this->sortField, $this->sortDirection);

    }

    public function getProductsProperty()
    {
       return $this->productsQuery->paginate(3);
    }

    /** RENDER **/

    public function render()
    {
        if ($this->selectAll) $this->selected= $this->produse->pluck('id')->map(fn($id) => (string) $id);

        return view('livewire.products-table', [
            'products' => $this->products,
        ]);
    }

     /** Livewire Lifecycle Hook **/

     public function updatedSelectate()
     {
        $this->selectAll = false;
        $this->selectPage = false;
     }

     public function updatedSelectPage($value)
     {

         $this->selectAll = false;
        // $this->selectate = [];

        if ($value) {
            $this->selected = $this->products->pluck('id')->map(fn($id) => (string) $id);
        } else {
            $this->selected = [];
        }
     }

    public function updatingSearch(): void
    {
        $this->gotoPage(1);
    }

}
