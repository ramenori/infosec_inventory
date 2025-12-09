{{-- resources/views/admin/inventory.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
  <h1 class="h3 my-3">INVENTORY</h1>

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="http://127.0.0.1:8000/admin/dashboard"><i class="bi bi-house"></i></a></li>
      <li class="breadcrumb-item active" aria-current="page">Inventory</li>
    </ol>
  </nav>

  {{-- Success Message --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- Top Buttons --}}
  <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
    <button type="button" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-clockwise"></i> Logs
    </button>
    <button type="button" class="btn btn-dark">Filter by</button>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
      Add Item
    </button>

    <div class="btn-group">
      <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        Export
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#">Export as Excel</a></li>
        <li><a class="dropdown-item" href="#">Export as PDF</a></li>
      </ul>
    </div>
  </div>

  {{-- Search Bar --}}
  <div class="mb-3 d-flex justify-content-end">
    <form method="GET" action="{{ route('admin.inventory') }}" class="d-flex">
      <input
        class="form-control me-2" 
        type="search" 
        name="search" 
        placeholder="Search" 
        aria-label="Search"
        value="{{ request('search') }}"
        style="max-width: 250px;"
      />
      <button class="btn btn-outline-secondary" type="submit" title="Search">
        <i class="bi bi-search"></i>
      </button>
    </form>
  </div>

  {{-- Inventory Table --}}
  <div class="table-responsive">
    <table class="table table-bordered text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>CATEGORY</th>
          <th>COMPONENT</th>
          <th>SERIAL NUM</th>
          <th>BRAND</th>
          <th>STOCK QTY</th>
          <th>DATE ADDED</th>
          <th>STATUS</th>
          <th>SUPPLIER</th>
          <th>ACTIONS</th>
        </tr>
      </thead>
      <tbody>
        @forelse($inventory as $item)
            <tr>
                <td>{{ $item->category }}</td>
                <td>{{ $item->component }}</td>
                <td>{{ $item->serial_num }}</td>
                <td>{{ $item->brand }}</td>
                <td>{{ $item->stock_qty }}</td>
                <td>{{ $item->date_added->format('Y-m-d') }}</td>
                <td>{{ $item->status }}</td>
                <td>{{ $item->supplier ? $item->supplier->name : 'N/A' }}</td>
                <td>
                    <a href="{{ route('admin.inventory.edit', $item->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('admin.inventory.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">No inventory items found.</td>
            </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination and entries info --}}
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div>Showing {{ $inventory->firstItem() }} to {{ $inventory->lastItem() }} of {{ $inventory->total() }} entries</div>
    {{ $inventory->links() }} <!-- Laravel pagination links -->
  </div>
</div>

{{-- Add Item Modal --}}
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addItemModalLabel">Add New Inventory Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.inventory.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-control" id="category" name="category" required>
              <option value="">Select Category</option>
              @foreach(\App\Models\Category::all() as $cat)
                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="component" class="form-label">Component</label>
            <input type="text" class="form-control" id="component" name="component" required>
          </div>
          <div class="mb-3">
            <label for="serial_num" class="form-label">Serial Number</label>
            <input type="text" class="form-control" id="serial_num" name="serial_num">
          </div>
          <div class="mb-3">
            <label for="brand" class="form-label">Brand</label>
            <input type="text" class="form-control" id="brand" name="brand">
          </div>
          <div class="mb-3">
            <label for="stock_qty" class="form-label">Stock Quantity</label>
            <input type="number" class="form-control" id="stock_qty" name="stock_qty" value="0" required>
          </div>
          <div class="mb-3">
            <label for="date_added" class="form-label">Date Added</label>
            <input type="date" class="form-control" id="date_added" name="date_added" required>
          </div>
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status" required>
              <option value="Available">Available</option>
              <option value="Deployed">Deployed</option>
              <option value="Out of Stock">Out of Stock</option>
            </select>
          </div>
          <div class="mb-3">
              <label for="supplier_id" class="form-label">Supplier</label>
              <select class="form-control" id="supplier_id" name="supplier_id">
                  <option value="">Select Supplier</option>
                  @foreach(\App\Models\Supplier::all() as $supplier)
                      <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                  @endforeach
              </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Add Item</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection