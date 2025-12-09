@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
  <h1 class="h3 my-3">DEPLOYMENT</h1>

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bi bi-house"></i></a></li>
      <li class="breadcrumb-item active" aria-current="page">Deployment</li>
    </ol>
  </nav>

  {{-- Success/Error Messages --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- Main Deployment Layout --}}
  <div class="row">
    {{-- Left Panel: Category & Component Selection --}}
    <div class="col-md-8">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Select Items to Deploy</h5>
        </div>
        <div class="card-body">
          
          {{-- Step 1: Select Category --}}
          <div class="mb-4">
            <div class="d-flex align-items-center mb-2">
              <span class="badge bg-primary me-2">Step 1</span>
              <h6 class="mb-0">Select a Category</h6>
            </div>
            
            @if(session('selected_category'))
              <div class="alert alert-info py-2 d-flex justify-content-between align-items-center">
                <div>
                  <strong>Selected Category:</strong> {{ session('selected_category_name') }}
                  <span class="badge bg-primary ms-2">{{ session('selected_category_available') }} available items</span>
                </div>
                <a href="{{ route('admin.deployment.clearCategory') }}" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-x-circle"></i> Change Category
                </a>
              </div>
            @endif
            
            <div class="row g-3">
              @forelse($categories as $category)
                <div class="col-md-4">
                  <div class="card h-100 border 
                    @if(session('selected_category') == $category->id) border-primary border-2 @endif
                    hover-shadow">
                    <div class="card-body text-center">
                      <h5 class="card-title">{{ $category->name }}</h5>
                      <div class="d-flex justify-content-center gap-3 mb-2">
                        <small class="text-muted">
                          <i class="bi bi-box"></i> Total: {{ $category->items_count }}
                        </small>
                        <small class="text-success">
                          <i class="bi bi-check-circle"></i> Available: {{ $category->available_count }}
                        </small>
                      </div>
                      
                      @if($category->available_count > 0)
                        <form action="{{ route('admin.deployment.selectCategory') }}" method="POST" class="mt-2">
                          @csrf
                          <input type="hidden" name="category_id" value="{{ $category->id }}">
                          <input type="hidden" name="category_name" value="{{ $category->name }}">
                          <input type="hidden" name="available_count" value="{{ $category->available_count }}">
                          <button type="submit" 
                            class="btn btn-sm 
                            @if(session('selected_category') == $category->id) btn-primary @else btn-outline-primary @endif
                            w-100">
                            @if(session('selected_category') == $category->id)
                              <i class="bi bi-check-lg"></i> Selected
                            @else
                              <i class="bi bi-plus-circle"></i> Select Category
                            @endif
                          </button>
                        </form>
                      @else
                        <button class="btn btn-sm btn-outline-secondary w-100" disabled>
                          <i class="bi bi-slash-circle"></i> No Available Items
                        </button>
                      @endif
                    </div>
                  </div>
                </div>
              @empty
                <div class="col-12">
                  <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle me-2"></i> No categories found
                  </div>
                </div>
              @endforelse
            </div>
          </div>

          {{-- Step 2: Select Components (Only shown if category is selected) --}}
          @if(session('selected_category'))
            <div class="mb-4">
              <div class="d-flex align-items-center mb-2">
                <span class="badge bg-success me-2">Step 2</span>
                <h6 class="mb-0">Select Components from {{ session('selected_category_name') }}</h6>
              </div>
              
              {{-- Search and Filter for Components --}}
              <div class="mb-3">
                <form method="GET" action="{{ route('admin.deployment') }}" class="row g-2">
                  <div class="col-md-8">
                    <input
                      class="form-control"
                      type="search"
                      name="component_search"
                      placeholder="Search components by name, serial, or brand..."
                      aria-label="Search"
                      value="{{ request('component_search') }}"
                    />
                  </div>
                  <div class="col-md-4">
                    <button class="btn btn-outline-secondary w-100" type="submit">
                      <i class="bi bi-search me-1"></i> Search
                    </button>
                  </div>
                </form>
              </div>
              
              {{-- Components Table --}}
              @if($categoryItems->count() > 0)
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                  <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light sticky-top">
                      <tr>
                        <th width="50">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllComponents">
                          </div>
                        </th>
                        <th>COMPONENT</th>
                        <th>SERIAL NUM</th>
                        <th>BRAND</th>
                        <th>STOCK QTY</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                      </tr>
                    </thead>
                    <tbody>
                      <form id="bulkAddForm" action="{{ route('admin.deployment.bulkAddToCart') }}" method="POST">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ session('selected_category') }}">
                        
                        @foreach($categoryItems as $item)
                          <tr>
                            <td>
                              @if($item->stock_qty > 0 && $item->status === 'Available')
                                <div class="form-check">
                                  <input class="form-check-input component-checkbox" 
                                         type="checkbox" 
                                         name="component_ids[]" 
                                         value="{{ $item->id }}"
                                         data-max-qty="{{ $item->stock_qty }}">
                                </div>
                              @endif
                            </td>
                            <td>{{ $item->component }}</td>
                            <td>{{ $item->serial_num ?? 'N/A' }}</td>
                            <td>{{ $item->brand ?? 'N/A' }}</td>
                            <td>
                              <span class="badge 
                                @if($item->stock_qty >= 10) bg-success
                                @elseif($item->stock_qty >= 5) bg-warning
                                @else bg-danger @endif">
                                {{ $item->stock_qty }}
                              </span>
                            </td>
                            <td>
                              <span class="badge 
                                @if($item->status === 'Available') bg-success
                                @elseif($item->status === 'Low Stock') bg-warning
                                @else bg-secondary @endif">
                                {{ $item->status }}
                              </span>
                            </td>
                            <td>
                              @if($item->stock_qty > 0 && $item->status === 'Available')
                                <div class="input-group input-group-sm" style="width: 150px;">
                                  <input type="number" 
                                         class="form-control quantity-input" 
                                         name="quantities[{{ $item->id }}]" 
                                         value="1" 
                                         min="1" 
                                         max="{{ $item->stock_qty }}"
                                         data-item-id="{{ $item->id }}"
                                         style="width: 60px;">
                                  <button type="button" 
                                          class="btn btn-success add-single-component"
                                          data-item-id="{{ $item->id }}"
                                          data-component="{{ $item->component }}">
                                    <i class="bi bi-plus"></i> Add
                                  </button>
                                </div>
                              @else
                                <span class="badge bg-secondary">Unavailable</span>
                              @endif
                            </td>
                          </tr>
                        @endforeach
                      </form>
                    </tbody>
                  </table>
                </div>
                
                {{-- Bulk Actions --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                  <div>
                    <span id="selectedCount" class="badge bg-info">0 items selected</span>
                  </div>
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="resetQuantities()">
                      <i class="bi bi-arrow-clockwise"></i> Reset Quantities
                    </button>
                    <button type="submit" form="bulkAddForm" class="btn btn-success">
                      <i class="bi bi-cart-plus"></i> Add Selected to Cart
                    </button>
                  </div>
                </div>
                
                {{-- Pagination --}}
                @if($categoryItems->hasPages())
                  <div class="d-flex justify-content-center mt-3">
                    {{ $categoryItems->links() }}
                  </div>
                @endif
              @else
                <div class="alert alert-info text-center">
                  <i class="bi bi-info-circle me-2"></i> No available components found in this category.
                </div>
              @endif
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Right Panel: Deployment Cart --}}
    <div class="col-md-4">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Deployment Cart</h5>
          @if($cartItems->count() > 0)
            <span class="badge bg-light text-dark">{{ $cartItems->sum('quantity') }} items</span>
          @endif
        </div>
        <div class="card-body">
          {{-- Cart Items List --}}
          <div style="max-height: 300px; overflow-y: auto;" class="mb-3">
            @if($cartItems->count() > 0)
              <ul class="list-group">
                @foreach($cartItems as $cartItem)
                  <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                      <div class="flex-grow-1 me-2">
                        <h6 class="mb-1">{{ $cartItem->inventory->component }}</h6>
                        <small class="text-muted">
                          <i class="bi bi-tag"></i> {{ $cartItem->inventory->category }}
                          @if($cartItem->inventory->serial_num)
                            | <i class="bi bi-upc"></i> {{ $cartItem->inventory->serial_num }}
                          @endif
                        </small>
                      </div>
                      <div class="d-flex flex-column align-items-end">
                        <form action="{{ route('admin.deployment.updateCart', $cartItem->id) }}" method="POST" class="d-flex align-items-center mb-1">
                          @csrf
                          @method('PUT')
                          <input type="number" 
                                 name="quantity" 
                                 class="form-control form-control-sm" 
                                 style="width: 70px;" 
                                 value="{{ $cartItem->quantity }}" 
                                 min="1" 
                                 max="{{ $cartItem->inventory->stock_qty + $cartItem->quantity }}"
                                 onchange="this.form.submit()">
                        </form>
                        <small class="text-muted">{{ $cartItem->quantity }} × item(s)</small>
                      </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                      <form action="{{ route('admin.deployment.removeFromCart', $cartItem->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                          <i class="bi bi-trash"></i> Remove
                        </button>
                      </form>
                    </div>
                  </li>
                @endforeach
              </ul>
            @else
              <div class="text-center text-muted py-4">
                <i class="bi bi-cart-x display-6"></i>
                <p class="mt-2">No items in cart</p>
                @if(!session('selected_category'))
                  <small class="text-info">Select a category first to add items</small>
                @endif
              </div>
            @endif
          </div>

          @if($cartItems->count() > 0)
            {{-- Cart Summary --}}
            <div class="border-top pt-3">
              <div class="d-flex justify-content-between mb-2">
                <span>Total Items:</span>
                <strong>{{ $cartItems->sum('quantity') }}</strong>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span>Unique Items:</span>
                <strong>{{ $cartItems->count() }}</strong>
              </div>
              <div class="d-flex justify-content-between mb-3">
                <span>Categories:</span>
                <strong>{{ $cartItems->pluck('inventory.category')->unique()->count() }}</strong>
              </div>
              
              {{-- Clear Cart Button --}}
              <form action="{{ route('admin.deployment.clearCart') }}" method="POST" class="mb-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Clear all items from cart?')">
                  <i class="bi bi-trash"></i> Clear Cart
                </button>
              </form>
              
              {{-- Deployment Details Form --}}
              <form action="{{ route('admin.deployment.deploy') }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Deployed To *</label>
                  <input type="text" class="form-control" name="deployed_to" 
                         placeholder="Employee name or Department" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Deployment Date *</label>
                  <input type="date" class="form-control" name="deployment_date" 
                         value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Remarks (optional)</label>
                  <textarea class="form-control" name="remarks" rows="2" 
                            placeholder="Additional notes..."></textarea>
                </div>

                {{-- Deploy Button --}}
                <button type="submit" class="btn btn-success w-100 mt-2" 
                        onclick="return confirm('Deploy {{ $cartItems->sum('quantity') }} item(s)?')">
                  <i class="bi bi-rocket"></i> Deploy Items
                </button>
              </form>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Recent Deployments --}}
  <div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">
      <h5 class="mb-0">Recent Deployments</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th>Deployment ID</th>
              <th>Items</th>
              <th>Deployed To</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentDeployments as $deployment)
              <tr>
                <td>#{{ str_pad($deployment->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $deployment->items_count }} items</td>
                <td>{{ $deployment->deployed_to }}</td>
                <td>{{ $deployment->deployment_date->format('Y-m-d') }}</td>
                <td><span class="badge bg-success">Completed</span></td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">No recent deployments</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- JavaScript for Enhanced Functionality --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Select All Components Checkbox
  const selectAllCheckbox = document.getElementById('selectAllComponents');
  const componentCheckboxes = document.querySelectorAll('.component-checkbox');
  
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
      componentCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      updateSelectedCount();
    });
  }
  
  // Update selected count
  function updateSelectedCount() {
    const selected = document.querySelectorAll('.component-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = `${selected} items selected`;
  }
  
  // Add event listeners to checkboxes
  componentCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
  });
  
  // Add single component button
  document.querySelectorAll('.add-single-component').forEach(button => {
    button.addEventListener('click', function() {
      const itemId = this.dataset.itemId;
      const componentName = this.dataset.component;
      const quantityInput = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
      const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
      
      // Submit form via AJAX
      fetch('{{ route("admin.deployment.addToCart") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          inventory_id: itemId,
          quantity: quantity
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message
          showToast('success', `${quantity} × ${componentName} added to cart`);
          // Reload page after a short delay
          setTimeout(() => location.reload(), 1000);
        } else {
          showToast('error', data.message || 'Failed to add item');
        }
      })
      .catch(error => {
        showToast('error', 'An error occurred');
      });
    });
  });
  
  // Reset quantities to 1
  function resetQuantities() {
    document.querySelectorAll('.quantity-input').forEach(input => {
      input.value = 1;
    });
  }
  
  // Toast notification function
  function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    `;
    
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove after hide
    toast.addEventListener('hidden.bs.toast', function () {
      document.body.removeChild(toast);
    });
  }
});
</script>
@endpush
@endsection