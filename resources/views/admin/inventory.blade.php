@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
  {{-- Header Section --}}
  <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
    <div>
      <h1 class="h2 mb-1 fw-bold text-gradient">Inventory</h1>
      <p class="text-muted mb-0">Manage all inventory items, stock levels, and status</p>
    </div>
  </div>

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
      <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
          <i class="bi bi-house-door"></i> Dashboard
        </a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        <i class="bi bi-boxes"></i> Inventory
      </li>
    </ol>
  </nav>

  {{-- Stats Cards (improved UI) --}}
  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card stat-card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="icon-circle bg-primary bg-opacity-10">
            <i class="bi bi-check-circle-fill text-primary"></i>
          </div>
          <div>
            <small class="text-muted">Available Items</small>
            <div class="d-flex align-items-baseline gap-3">
              <h2 class="stat-number mb-0">{{ \App\Models\Inventory::where('status', 'Available')->count() }}</h2>
              <small class="text-muted">Ready for deployment</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card stat-card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="icon-circle bg-warning bg-opacity-10">
            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
          </div>
          <div>
            <small class="text-muted">Low Stock</small>
            <div class="d-flex align-items-baseline gap-3">
              <h2 class="stat-number mb-0">{{ \App\Models\Inventory::where('stock_qty', '<', 5)->count() }}</h2>
              <small class="text-muted">Below reorder threshold</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card stat-card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="icon-circle bg-info bg-opacity-10">
            <i class="bi bi-tags-fill text-info"></i>
          </div>
          <div>
            <small class="text-muted">Total Categories</small>
            <div class="d-flex align-items-baseline gap-3">
              <h2 class="stat-number mb-0">{{ \App\Models\Category::count() }}</h2>
              <small class="text-muted">Inventory groups</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Controls Section --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="row align-items-center">
        <div class="col-md-6 mb-3 mb-md-0">
          <form method="GET" action="{{ route('admin.inventory') }}" class="row g-2">
            <div class="col-auto">
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                  <i class="bi bi-search"></i>
                </span>
                <input type="search" 
                       class="form-control border-start-0" 
                       name="search" 
                       placeholder="Search inventory..." 
                       value="{{ request('search') }}">
              </div>
            </div>
            <div class="col-auto">
              <select class="form-select" name="category">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>
                    {{ $cat->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-search me-1"></i> Search
              </button>
              @if(request('search'))
                <a href="{{ route('admin.inventory') }}" class="btn btn-outline-secondary">
                  <i class="bi bi-x-circle"></i>
                </a>
              @endif
            </div>
          </form>
        </div>
        
        <div class="col-md-6">
          <div class="d-flex justify-content-end align-items-center gap-2">
            <div class="dropdown">
              <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-filter me-1"></i> Filter
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('admin.inventory', ['status' => 'Available']) }}">
                  <span class="badge bg-success me-2">●</span> Available Items
                </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.inventory', ['status' => 'Low Stock']) }}">
                  <span class="badge bg-warning me-2">●</span> Low Stock
                </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.inventory', ['status' => 'Out of Stock']) }}">
                  <span class="badge bg-danger me-2">●</span> Out of Stock
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header">Categories</h6></li>
                @foreach($categories as $cat)
                  <li><a class="dropdown-item" href="{{ route('admin.inventory', ['category' => $cat->name]) }}">
                    <i class="bi bi-folder me-2"></i> {{ $cat->name }}
                  </a></li>
                @endforeach
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('admin.inventory') }}">
                  <i class="bi bi-eye me-2"></i> View All
                </a></li>
              </ul>
            </div>

            <a href="{{ route('admin.inventory.logs') }}" class="btn btn-outline-info">
              <i class="bi bi-clock-history me-1"></i> Logs
            </a>
            
            <div class="dropdown">
              <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-download me-1"></i> Export
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a class="dropdown-item" href="{{ route('admin.inventory.export.csv', request()->query()) }}">
                    <i class="bi bi-file-earmark-excel me-2"></i> Excel (CSV)
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('admin.inventory.export.pdf', request()->query()) }}">
                    <i class="bi bi-file-earmark-pdf me-2"></i> PDF
                  </a>
                </li>
              </ul>
            </div>
            
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
              <i class="bi bi-plus-circle me-1"></i> Add Item
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Inventory Table --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-light py-3">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">
          <i class="bi bi-table me-2"></i> Inventory Items
        </h6>
        <small class="text-muted">
          Showing {{ $inventory->firstItem() }} to {{ $inventory->lastItem() }} of {{ $inventory->total() }} entries
        </small>
      </div>
    </div>
    
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="border-0" width="50">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="selectAll">
                </div>
              </th>
              <th class="border-0">ITEM</th>
              <th class="border-0 text-center">CATEGORY</th>
              <th class="border-0 text-center">STOCK</th>
              <th class="border-0 text-center">STATUS</th>
              <th class="border-0 text-center">DATE ADDED</th>
              <th class="border-0 text-center">SUPPLIER</th>
              <th class="border-0 text-center">ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            @forelse($inventory as $item)
              <tr class="hover-shadow">
                <td>
                  <div class="form-check">
                    <input class="form-check-input select-item" type="checkbox" value="{{ $item->id }}">
                  </div>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="item-icon me-3">
                      <i class="bi bi-box text-primary fs-4"></i>
                    </div>
                    <div>
                      <h6 class="mb-0 fw-semibold">{{ $item->component }}</h6>
                      <div class="text-muted small">
                        <span class="me-2">
                          <i class="bi bi-upc-scan"></i> {{ $item->serial_num ?: 'No Serial' }}
                        </span>
                        @if($item->brand)
                          <span><i class="bi bi-tag"></i> {{ $item->brand }}</span>
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
                <td class="text-center">
                    <i class="bi bi-folder me-1"></i> {{ $item->category }}
                </td>
                <td class="text-center">
                  <div class="stock-indicator">
                    <div class="progress" style="height: 8px; width: 100px; margin: 0 auto;">
                      @php
                        $percentage = min(100, ($item->stock_qty / 20) * 100);
                        $color = $item->stock_qty >= 10 ? 'bg-success' : ($item->stock_qty >= 5 ? 'bg-warning' : 'bg-danger');
                      @endphp
                      <div class="progress-bar {{ $color }}" style="width: {{ $percentage }}%"></div>
                    </div>
                    <span class="fw-bold mt-1 d-block">{{ $item->stock_qty }}</span>
                  </div>
                </td>
                <td class="text-center">
                  <span class="status-badge-modern 
                    @if($item->status === 'Available') status-available
                    @elseif($item->status === 'Low Stock') status-low-stock
                    @elseif($item->status === 'Out of Stock') status-out-of-stock
                    @else status-default @endif">
                    <span class="status-dot"></span>
                    {{ $item->status }}
                  </span>
                </td>
                <td class="text-center">
                  <div class="d-flex flex-column">
                    <span class="fw-semibold">{{ $item->date_added->format('M d, Y') }}</span>
                    <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                  </div>
                </td>
                <td class="text-center">
                  @if($item->supplier)
                    <div class="d-flex flex-column align-items-center">
                        <i class="bi bi-truck me-1"></i> {{ $item->supplier->name }}
                      @if($item->supplier->contact)
                        <small class="text-muted mt-1">{{ $item->supplier->contact }}</small>
                      @endif
                    </div>
                  @else
                    <span class="badge bg-secondary">No Supplier</span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    <button type="button"
                            class="btn btn-sm btn-outline-primary edit-details-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#editInventoryModal"
                            data-item-id="{{ $item->id }}"
                            data-component="{{ $item->component }}"
                            data-serial="{{ $item->serial_num ?: '' }}"
                            data-brand="{{ $item->brand ?: '' }}"
                            data-category="{{ $item->category }}"
                            data-stock="{{ $item->stock_qty }}"
                            data-date="{{ $item->date_added->format('Y-m-d') }}"
                            data-status="{{ $item->status }}"
                            data-supplier-id="{{ $item->supplier_id ?? '' }}"
                            data-bs-toggle="tooltip"
                            title="Edit Item">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button"
                            class="btn btn-sm btn-outline-info view-details-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#inventoryDetailsModal"
                            data-component="{{ $item->component }}"
                            data-serial="{{ $item->serial_num ?: 'No Serial' }}"
                            data-brand="{{ $item->brand ?: 'No Brand' }}"
                            data-category="{{ $item->category }}"
                            data-stock="{{ $item->stock_qty }}"
                            data-status="{{ $item->status }}"
                            data-date="{{ $item->date_added->format('M d, Y') }}"
                            data-created="{{ $item->created_at->diffForHumans() }}"
                            data-supplier="{{ $item->supplier ? $item->supplier->name : 'No Supplier' }}"
                            data-supplier-contact="{{ $item->supplier && $item->supplier->contact ? $item->supplier->contact : '' }}"
                            data-description="{{ e($item->description ?: 'No description provided.') }}"
                            data-bs-toggle="tooltip"
                            title="View Details">
                      <i class="bi bi-eye"></i>
                    </button>
                    <form action="{{ route('admin.inventory.destroy', $item->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" 
                              class="btn btn-sm btn-outline-danger"
                              data-bs-toggle="tooltip"
                              title="Delete Item"
                              onclick="return confirm('Are you sure you want to delete this item?')">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-5">
                  <div class="empty-state">
                    <i class="bi bi-inboxes display-4 text-muted mb-3"></i>
                    <h5 class="text-muted">No inventory items found</h5>
                  <!-- .stat-card { border-radius: .6rem; }
                  .stat-number { font-size: 1.9rem; }
                  .stat-icon { width: 56px; height: 56px; display:flex; align-items:center; justify-content:center; }
                  .icon-circle { width:56px; height:56px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
                  .icon-circle i { font-size:1.4rem; }
                  .bg-opacity-10 { opacity: .12; } -->
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    
    {{-- Footer with Pagination --}}
    @if($inventory->hasPages())
      <div class="card-footer bg-light py-3">
        <div class="d-flex justify-content-between align-items-center">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="selectAllFooter">
            <label class="form-check-label small text-muted" for="selectAllFooter">
              Select All ({{ $inventory->count() }} items)
            </label>
          </div>
          <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
              {{ $inventory->links() }}
            </ul>
          </nav>
        </div>
      </div>
    @endif
  </div>
</div>

{{-- Add Item Modal --}}
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addItemModalLabel">
          <i class="bi bi-plus-circle me-2"></i> Add New Inventory Item
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.inventory.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            {{-- Row 1 --}}
            <div class="col-md-6">
              <label for="category" class="form-label small fw-semibold">
                <i class="bi bi-folder me-1"></i> Category *
              </label>
              <select class="form-select" id="category" name="category" required>
                <option value="">Select Category</option>
                @foreach(\App\Models\Category::all() as $cat)
                  <option value="{{ $cat->name }}" {{ old('category') == $cat->name ? 'selected' : '' }}>
                    {{ $cat->name }}
                  </option>
                @endforeach
              </select>
            </div>
            
            <div class="col-md-6">
              <label for="component" class="form-label small fw-semibold">
                <i class="bi bi-box me-1"></i> Component Name *
              </label>
              <input type="text" class="form-control" id="component" name="component" 
                     value="{{ old('component') }}" placeholder="Enter component name" required>
            </div>
            
            {{-- Row 2 --}}
            <div class="col-md-6">
              <label for="serial_num" class="form-label small fw-semibold">
                <i class="bi bi-upc-scan me-1"></i> Serial Number
              </label>
              <input type="text" class="form-control" id="serial_num" name="serial_num" 
                     value="{{ old('serial_num') }}" placeholder="Enter serial number">
            </div>
            
            <div class="col-md-6">
              <label for="brand" class="form-label small fw-semibold">
                <i class="bi bi-tag me-1"></i> Brand
              </label>
              <input type="text" class="form-control" id="brand" name="brand" 
                     value="{{ old('brand') }}" placeholder="Enter brand name">
            </div>
            
            {{-- Row 3 --}}
            <div class="col-md-6">
              <label for="stock_qty" class="form-label small fw-semibold">
                <i class="bi bi-box-arrow-in-down me-1"></i> Stock Quantity *
              </label>
              <div class="input-group">
                <input type="number" class="form-control" id="stock_qty" name="stock_qty" 
                       value="{{ old('stock_qty', 1) }}" min="0" required>
                <span class="input-group-text">units</span>
              </div>
              <small class="text-muted">Enter initial stock quantity</small>
            </div>
            
            <div class="col-md-6">
              <label for="status" class="form-label small fw-semibold">
                <i class="bi bi-circle-fill me-1"></i> Status *
              </label>
              <select class="form-select" id="status" name="status" required>
                <option value="Available" {{ old('status') == 'Available' ? 'selected' : '' }}>Available</option>
                <option value="Low Stock" {{ old('status') == 'Low Stock' ? 'selected' : '' }}>Low Stock</option>
                <option value="Out of Stock" {{ old('status') == 'Out of Stock' ? 'selected' : '' }}>Out of Stock</option>
                <option value="Deployed" {{ old('status') == 'Deployed' ? 'selected' : '' }}>Deployed</option>
              </select>
              <small class="text-muted">Status will auto-update based on stock level</small>
            </div>
            
            {{-- Row 4 --}}
            <div class="col-md-6">
              <label for="supplier_id" class="form-label small fw-semibold">
                <i class="bi bi-truck me-1"></i> Supplier
              </label>
              <select class="form-select" id="supplier_id" name="supplier_id">
                <option value="">Select Supplier</option>
                @foreach(\App\Models\Supplier::all() as $supplier)
                  <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->name }}
                    @if($supplier->contact) - {{ $supplier->contact }} @endif
                  </option>
                @endforeach
              </select>
            </div>
            
            <div class="col-md-6">
              <div class="card bg-light border">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-calendar-date text-primary fs-4 me-3"></i>
                    <div>
                      <h6 class="mb-0 fw-semibold">Date Added</h6>
                      <p class="mb-0 text-muted small">Automatically set to today's date</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            {{-- Row 5 --}}
            <div class="col-12">
              <label for="description" class="form-label small fw-semibold">
                <i class="bi bi-card-text me-1"></i> Description (Optional)
              </label>
              <textarea class="form-control" id="description" name="description" 
                        rows="2" placeholder="Enter item description">{{ old('description') }}</textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add Item
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Inventory Details Modal --}}
<div class="modal fade" id="inventoryDetailsModal" tabindex="-1" aria-labelledby="inventoryDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="inventoryDetailsModalLabel">
          <i class="bi bi-box-seam me-2"></i> Inventory Item Details
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <h6 class="text-muted mb-1">Component</h6>
            <p class="fw-semibold mb-0" id="detailComponent">-</p>
          </div>
          <div class="col-md-6">
            <h6 class="text-muted mb-1">Category</h6>
            <p class="fw-semibold mb-0" id="detailCategory">-</p>
          </div>
          <div class="col-md-6">
            <h6 class="text-muted mb-1">Serial Number</h6>
            <p class="fw-semibold mb-0" id="detailSerial">-</p>
          </div>
          <div class="col-md-6">
            <h6 class="text-muted mb-1">Brand</h6>
            <p class="fw-semibold mb-0" id="detailBrand">-</p>
          </div>
          <div class="col-md-6">
            <h6 class="text-muted mb-1">Stock Quantity</h6>
            <p class="fw-semibold mb-0" id="detailStock">-</p>
          </div>
          <div class="col-md-6">
            <h6 class="text-muted mb-1">Status</h6>
            <p class="fw-semibold mb-0" id="detailStatus">-</p>
          </div>
          <div class="col-md-6">
            <h6 class="text-muted mb-1">Date Added</h6>
            <p class="fw-semibold mb-0" id="detailDate">-</p>
          </div>
          <div class="col-md-6">
            <h6 class="text-muted mb-1">Supplier</h6>
            <p class="fw-semibold mb-0" id="detailSupplier">-</p>
          </div>
        </div>
        <div class="mt-4">
          <h6 class="text-muted mb-2">Description</h6>
          <p class="mb-0" id="detailDescription">-</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const detailButtons = document.querySelectorAll('.view-details-btn');

  detailButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      document.getElementById('detailComponent').textContent = button.dataset.component || 'N/A';
      document.getElementById('detailCategory').textContent = button.dataset.category || 'N/A';
      document.getElementById('detailSerial').textContent = button.dataset.serial || 'N/A';
      document.getElementById('detailBrand').textContent = button.dataset.brand || 'N/A';
      document.getElementById('detailStock').textContent = button.dataset.stock || 'N/A';
      document.getElementById('detailStatus').textContent = button.dataset.status || 'N/A';
      document.getElementById('detailDate').textContent = button.dataset.date || 'N/A';
      document.getElementById('detailDescription').textContent = button.dataset.description || 'No description provided.';

      const supplierName = button.dataset.supplier || 'No Supplier';
      const supplierContact = button.dataset.supplierContact;
      document.getElementById('detailSupplier').textContent = supplierContact ? `${supplierName} • ${supplierContact}` : supplierName;
    });
  });

  const editButtons = document.querySelectorAll('.edit-details-btn');

  editButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      document.getElementById('editInventoryForm').action = `/admin/inventory/${button.dataset.itemId}`;
      document.getElementById('editCategory').value = button.dataset.category || '';
      document.getElementById('editComponent').value = button.dataset.component || '';
      document.getElementById('editSerial').value = button.dataset.serial || '';
      document.getElementById('editBrand').value = button.dataset.brand || '';
      document.getElementById('editStock').value = button.dataset.stock || '';
      document.getElementById('editDate').value = button.dataset.date || '';
      document.getElementById('editStatus').value = button.dataset.status || 'Available';
      document.getElementById('editSupplierId').value = button.dataset.supplierId || '';
    });
  });
});
</script>

{{-- Edit Inventory Modal --}}
<div class="modal fade" id="editInventoryModal" tabindex="-1" aria-labelledby="editInventoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="editInventoryModalLabel">
          <i class="bi bi-pencil-square me-2"></i> Edit Inventory Item
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editInventoryForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="editCategory" class="form-label small fw-semibold">
                <i class="bi bi-folder me-1"></i> Category *
              </label>
              <select class="form-select" id="editCategory" name="category" required>
                <option value="">Select Category</option>
                @foreach(
App\Models\Category::all() as $cat)
                  <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="editComponent" class="form-label small fw-semibold">
                <i class="bi bi-box me-1"></i> Component Name *
              </label>
              <input type="text" class="form-control" id="editComponent" name="component" required>
            </div>

            <div class="col-md-6">
              <label for="editSerial" class="form-label small fw-semibold">
                <i class="bi bi-upc-scan me-1"></i> Serial Number
              </label>
              <input type="text" class="form-control" id="editSerial" name="serial_num">
            </div>

            <div class="col-md-6">
              <label for="editBrand" class="form-label small fw-semibold">
                <i class="bi bi-tag me-1"></i> Brand
              </label>
              <input type="text" class="form-control" id="editBrand" name="brand">
            </div>

            <div class="col-md-6">
              <label for="editStock" class="form-label small fw-semibold">
                <i class="bi bi-box-arrow-in-down me-1"></i> Stock Quantity *
              </label>
              <input type="number" class="form-control" id="editStock" name="stock_qty" min="0" required>
            </div>

            <div class="col-md-6">
              <label for="editStatus" class="form-label small fw-semibold">
                <i class="bi bi-circle-fill me-1"></i> Status *
              </label>
              <select class="form-select" id="editStatus" name="status" required>
                <option value="Available">Available</option>
                <option value="Low Stock">Low Stock</option>
                <option value="Out of Stock">Out of Stock</option>
                <option value="Maintenance">Maintenance</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="editDate" class="form-label small fw-semibold">
                <i class="bi bi-calendar-date me-1"></i> Date Added *
              </label>
              <input type="date" class="form-control" id="editDate" name="date_added" required>
            </div>

            <div class="col-md-6">
              <label for="editSupplierId" class="form-label small fw-semibold">
                <i class="bi bi-truck me-1"></i> Supplier
              </label>
              <select class="form-select" id="editSupplierId" name="supplier_id">
                <option value="">Select Supplier</option>
                @foreach(\App\Models\Supplier::all() as $supplier)
                  <option value="{{ $supplier->id }}">{{ $supplier->name }}@if($supplier->contact) - {{ $supplier->contact }} @endif</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Custom CSS --}}
<style>
.stat-card {
  border-radius: 10px;
  transition: transform 0.3s ease;
}

.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
.stat-card:hover {
  transform: translateY(-5px);
}
.stat-icon {
  font-size: 3rem;
  opacity: 0.3;
}
/* Modern Status Badges */
.status-badge-modern {
  padding: 6px 14px;
  border-radius: 30px;
  font-size: 0.75rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  letter-spacing: 0.5px;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.status-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  display: inline-block;
}

/* Available Status Design (Soft Green) */
.status-available {
  background-color: rgba(25, 135, 84, 0.12);
  color: #1f8b4c;
  border: 1px solid rgba(25, 135, 84, 0.2);
}
.status-available .status-dot {
  background-color: #198754;
  box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.25);
  animation: pulse-green 2s infinite;
}

/* Low Stock Status Design (Soft Amber) */
.status-low-stock {
  background-color: rgba(255, 193, 7, 0.15);
  color: #a06e00;
  border: 1px solid rgba(255, 193, 7, 0.25);
}
.status-low-stock .status-dot {
  background-color: #ffb700;
  box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.3);
}

/* Out of Stock Status Design (Soft Red/Rose) */
.status-out-of-stock {
  background-color: rgba(220, 53, 69, 0.1);
  color: #d12a3a;
  border: 1px solid rgba(220, 53, 69, 0.18);
}
.status-out-of-stock .status-dot {
  background-color: #dc3545;
  box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.25);
}

/* Default / Other Status Design (Soft Slate) */
.status-default {
  background-color: rgba(108, 117, 125, 0.1);
  color: #5c636a;
  border: 1px solid rgba(108, 117, 125, 0.15);
}
.status-default .status-dot {
  background-color: #6c757d;
}

/* Pulse animation for the "Available" status dot to show active system status */
@keyframes pulse-green {
  0% {
    box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4);
  }
  70% {
    box-shadow: 0 0 0 6px rgba(25, 135, 84, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
  }
}
.hover-shadow:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  transition: all 0.3s ease;
}
.empty-state {
  padding: 3rem 1rem;
}
.table tbody tr {
  transition: all 0.2s ease;
}
.progress {
  border-radius: 10px;
}
.bg-gradient {
  background: linear-gradient(135deg, var(--bs-primary) 0%, #0a58ca 100%);
}
.bg-gradient.warning {
  background: linear-gradient(135deg, var(--bs-warning) 0%, #e0a800 100%);
}
.bg-gradient.info {
  background: linear-gradient(135deg, var(--bs-info) 0%, #0aa2c0 100%);
}
.bg-gradient.success {
  background: linear-gradient(135deg, var(--bs-success) 0%, #146c43 100%);
}
.item-icon {
  width: 40px;
  height: 40px;
  background: rgba(13, 110, 253, 0.1);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>

{{-- JavaScript --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Select All functionality
  const selectAll = document.getElementById('selectAll');
  const selectAllFooter = document.getElementById('selectAllFooter');
  const checkboxes = document.querySelectorAll('.select-item');
  
  function updateSelectAll() {
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    if (selectAll) selectAll.checked = allChecked;
    if (selectAllFooter) selectAllFooter.checked = allChecked;
  }
  
  if (selectAll) {
    selectAll.addEventListener('change', function() {
      checkboxes.forEach(cb => cb.checked = this.checked);
    });
  }
  
  if (selectAllFooter) {
    selectAllFooter.addEventListener('change', function() {
      checkboxes.forEach(cb => cb.checked = this.checked);
    });
  }
  
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectAll);
  });
  
  // Initialize tooltips
  const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  tooltips.forEach(tooltip => {
    new bootstrap.Tooltip(tooltip);
  });
  
  // Auto-focus search on page load if there's a search query
  @if(request('search'))
    document.querySelector('input[name="search"]')?.focus();
  @endif
  
  // Auto-update status based on stock quantity
  const stockQtyInput = document.getElementById('stock_qty');
  const statusSelect = document.getElementById('status');
  
  if (stockQtyInput && statusSelect) {
    stockQtyInput.addEventListener('input', function() {
      const qty = parseInt(this.value) || 0;
      
      if (qty === 0) {
        statusSelect.value = 'Out of Stock';
      } else if (qty < 5) {
        statusSelect.value = 'Low Stock';
      } else {
        statusSelect.value = 'Available';
      }
    });
  }

});
</script>
@endpush

@if(session('success'))
  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      showNotification('success', '{{ session('success') }}');
    });
    
    function showNotification(type, message) {
      const alert = document.createElement('div');
      alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
      alert.style.top = '20px';
      alert.style.right = '20px';
      alert.style.zIndex = '9999';
      alert.style.minWidth = '300px';
      alert.innerHTML = `
        <div class="d-flex align-items-center">
          <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'} me-2"></i>
          <div>${message}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      `;
      document.body.appendChild(alert);
      
      setTimeout(() => {
        if (alert.parentNode) {
          alert.remove();
        }
      }, 5000);
    }
  </script>
  @endpush
@endif
@endsection