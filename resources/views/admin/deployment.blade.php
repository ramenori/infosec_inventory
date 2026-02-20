@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
  {{-- Header with Stats --}}
  <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
    <div>
      <h1 class="h2 mb-1 fw-bold text-gradient">Deployment</h1>
      <p class="text-muted mb-0">Deploy inventory items to employees or departments</p>
    </div>
    <div class="d-flex gap-2">
      <div class="stat-card bg-primary text-white">
        <small class="d-block">Available Items</small>
        <h4 class="mb-0 fw-bold">{{ $categories->sum('available_count') }}</h4>
      </div>
      <div class="stat-card bg-success text-white">
        <small class="d-block">In Cart</small>
        <h4 class="mb-0 fw-bold">{{ $cartItems->sum('quantity') }}</h4>
      </div>
    </div>
  </div>

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
      <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
          <i class="bi bi-house-door-fill"></i> Dashboard
        </a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        <i class="bi bi-truck"></i> Deployment
      </li>
    </ol>
  </nav>

  {{-- Notifications --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>
      <div class="flex-grow-1">{{ session('success') }}</div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
      <i class="bi bi-exclamation-circle-fill me-2"></i>
      <div class="flex-grow-1">{{ session('error') }}</div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- Main Content --}}
  <div class="row g-4">
    {{-- Left Column: Item Selection --}}
    <div class="col-lg-8">
      {{-- Category Selection Card --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-gradient-primary text-white py-3">
          <div class="d-flex align-items-center">
            <div class="step-badge me-3">1</div>
            <h5 class="mb-0">Select Category</h5>
            @if(session('selected_category'))
              <div class="ms-auto">
                <span class="badge bg-white text-primary">
                  {{ session('selected_category_name') }}
                  <a href="{{ route('admin.deployment.clearCategory') }}" class="text-danger ms-1">
                    <i class="bi bi-x-circle"></i>
                  </a>
                </span>
              </div>
            @endif
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">
            @forelse($categories as $category)
              <div class="col-md-4 col-sm-6">
                <div class="category-card card h-100 border-0 hover-lift 
                  @if(session('selected_category') == $category->id) selected shadow @else shadow-sm @endif"
                  data-category-id="{{ $category->id }}"
                  data-available="{{ $category->available_count }}">
                  <div class="card-body d-flex flex-column align-items-center text-center p-4">
  
                  {{-- Icon --}}
                  <div class="category-icon mb-3">
                    @php
                      $icons = [
                        'Access Control'  => 'bi-shield-lock',
                        'CCTV'            => 'bi-camera-video',
                        'GPS'             => 'bi-geo-alt',
                        'Wireless Alarm'  => 'bi-bell',
                        'Computers'       => 'bi-laptop',
                        'Electronics'     => 'bi-cpu',
                        'Furniture'       => 'bi-chair',
                        'Office Supplies' => 'bi-briefcase',
                        'Network'         => 'bi-wifi',
                      ];
                      $icon = $icons[$category->name] ?? 'bi-box-seam';
                    @endphp
                    <i class="bi {{ $icon }} fs-1 
                      @if($category->available_count > 0) text-primary @else text-muted @endif">
                    </i>
                  </div>

                  {{-- Category Name --}}
                  <h6 class="card-title fw-semibold mb-3 w-100">{{ $category->name }}</h6>

                  {{-- Stats --}}
                  <div class="category-stats d-flex justify-content-center gap-4 mb-3 w-100">
                    <div>
                      <small class="text-muted d-block">Total</small>
                      <span class="fw-bold">{{ $category->items_count }}</span>
                    </div>
                    <div>
                      <small class="text-muted d-block">Available</small>
                      <span class="fw-bold text-success">{{ $category->available_count }}</span>
                    </div>
                  </div>

                  {{-- Button --}}
                  @if($category->available_count > 0)
                    <form action="{{ route('admin.deployment.selectCategory') }}" method="POST" class="w-100">
                      @csrf
                      <input type="hidden" name="category_id" value="{{ $category->id }}">
                      <input type="hidden" name="category_name" value="{{ $category->name }}">
                      <input type="hidden" name="available_count" value="{{ $category->available_count }}">
                      <button type="submit" class="btn btn-sm w-100
                        @if(session('selected_category') == $category->id) btn-primary @else btn-outline-primary @endif">
                        @if(session('selected_category') == $category->id)
                          <i class="bi bi-check-lg me-1"></i> Selected
                        @else
                          <i class="bi bi-plus-circle me-1"></i> Select
                        @endif
                      </button>
                    </form>
                  @else
                    <button class="btn btn-sm btn-outline-secondary w-100" disabled>
                      <i class="bi bi-slash-circle me-1"></i> Unavailable
                    </button>
                  @endif

                </div>
                </div>
              </div>
            @empty
              <div class="col-12">
                <div class="text-center py-5">
                  <i class="bi bi-folder-x fs-1 text-muted"></i>
                  <p class="text-muted mt-2">No categories found</p>
                </div>
              </div>
            @endforelse
          </div>
        </div>
      </div>

      {{-- Component Selection Card --}}
      @if(session('selected_category'))
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-gradient-success text-white py-3">
            <div class="d-flex align-items-center">
              <div class="step-badge me-3">2</div>
              <h5 class="mb-0">Select Items from {{ session('selected_category_name') }}</h5>
              <div class="ms-auto">
                <span class="badge bg-white text-success">
                  {{ $categoryItems->total() }} items found
                </span>
              </div>
            </div>
          </div>
          <div class="card-body">
            {{-- Search Bar --}}
            <div class="row g-3 mb-4">
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-search"></i>
                  </span>
                  <input type="search" 
                         class="form-control border-start-0" 
                         placeholder="Search by name, serial, or brand..."
                         name="component_search"
                         value="{{ request('component_search') }}"
                         form="searchForm">
                </div>
              </div>
              <div class="col-md-4">
                <form id="searchForm" method="GET" action="{{ route('admin.deployment') }}" class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Search
                  </button>
                  @if(request('component_search'))
                    <a href="{{ route('admin.deployment') }}" class="btn btn-outline-secondary">
                      <i class="bi bi-x-circle"></i>
                    </a>
                  @endif
                </form>
              </div>
            </div>

            {{-- Components Table --}}
            @if($categoryItems->count() > 0)
              <div class="table-responsive border rounded">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th width="50">
                      </th>
                      <th>Item</th>
                      <th>Brand</th>
                      <th>Serial</th>
                      <th class="text-center">Stock</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <form id="bulkAddForm" action="{{ route('admin.deployment.bulkAddToCart') }}" method="POST">
                      @csrf
                      <input type="hidden" name="category_id" value="{{ session('selected_category') }}">
                      
                      @foreach($categoryItems as $item)
                        <tr class="@if($item->stock_qty == 0 || $item->status !== 'Available') table-secondary @endif">
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
                          <td>
                            <div>
                              <strong class="d-block">{{ $item->component }}</strong>
                              <small class="text-muted">{{ $item->description ?? 'No description' }}</small>
                            </div>
                          </td>
                          <td>{{ $item->brand ?? 'N/A' }}</td>
                          <td>
                            <code class="text-muted">{{ $item->serial_num ?? 'N/A' }}</code>
                          </td>
                          <td class="text-center">
                            <span class="badge 
                              @if($item->stock_qty >= 10) bg-success
                              @elseif($item->stock_qty >= 5) bg-warning text-dark
                              @else bg-danger @endif
                              px-3 py-2">
                              {{ $item->stock_qty }}
                            </span>
                          </td>
                          <td class="text-center">
                            <span class="status-badge 
                              @if($item->status === 'Available') bg-success
                              @elseif($item->status === 'Low Stock') bg-warning text-dark
                              @else bg-secondary @endif">
                              {{ $item->status }}
                            </span>
                          </td>
                          <td class="text-center">
                            @if($item->stock_qty > 0 && $item->status === 'Available')
                              <div class="d-flex align-items-center justify-content-center">
                                <div class="quantity-control me-2">
                                  <button type="button" class="btn btn-sm btn-outline-secondary decrement" 
                                          data-target="qty-{{ $item->id }}">
                                    <i class="bi bi-dash"></i>
                                  </button>
                                  <input type="number" 
                                         id="qty-{{ $item->id }}"
                                         class="form-control form-control-sm text-center quantity-input" 
                                         style="width: 60px;"
                                         value="1" 
                                         min="1" 
                                         max="{{ $item->stock_qty }}"
                                         data-item-id="{{ $item->id }}">
                                  <button type="button" class="btn btn-sm btn-outline-secondary increment" 
                                          data-target="qty-{{ $item->id }}">
                                    <i class="bi bi-plus"></i>
                                  </button>
                                </div>
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
              <div class="d-flex justify-content-between align-items-center mt-4 p-3 bg-light rounded">
                <div>
                  <span id="selectedCount" class="badge bg-primary px-3 py-2">0 items selected</span>
                  <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="resetQuantities()">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                  </button>
                </div>
                <div class="d-flex gap-2">
                  <button type="submit" form="bulkAddForm" class="btn btn-success">
                    <i class="bi bi-cart-plus me-1"></i> Add Selected to Cart
                  </button>
                </div>
              </div>

              {{-- Pagination --}}
              @if($categoryItems->hasPages())
                <div class="d-flex justify-content-center mt-4">
                  <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm">
                      {{ $categoryItems->links() }}
                    </ul>
                  </nav>
                </div>
              @endif
            @else
              <div class="text-center py-5">
                <i class="bi bi-inboxes fs-1 text-muted"></i>
                <p class="text-muted mt-2">No items found in this category</p>
                @if(request('component_search'))
                  <a href="{{ route('admin.deployment') }}" class="btn btn-sm btn-outline-primary mt-2">
                    Clear Search
                  </a>
                @endif
              </div>
            @endif
          </div>
        </div>
      @endif
    </div>

    {{-- Right Column: Deployment Cart --}}
    <div class="col-lg-4">
      <div class="sticky-top" style="top: 20px;">
        {{-- Cart Card --}}
        <div class="card border-0 shadow-lg">
          <div class="card-header bg-gradient-info text-white py-3">
            <div class="d-flex align-items-center">
              <i class="bi bi-cart-check fs-4 me-2"></i>
              <h5 class="mb-0">Deployment Cart</h5>
              @if($cartItems->count() > 0)
                <span class="badge bg-white text-info ms-auto">
                  {{ $cartItems->sum('quantity') }} items
                </span>
              @endif
            </div>
          </div>
          
          <div class="card-body">
            @if($cartItems->count() > 0)
              {{-- Cart Items --}}
              <div class="cart-items mb-4" style="max-height: 300px; overflow-y: auto;">
                @foreach($cartItems as $cartItem)
                  <div class="cart-item card border mb-3">
                    <div class="card-body p-3">
                      <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1 me-3">
                          <h6 class="mb-1 fw-semibold">{{ $cartItem->inventory->component }}</h6>
                          <div class="d-flex flex-wrap gap-2 mb-2">
                            <small class="text-muted">
                              <i class="bi bi-tag"></i> {{ $cartItem->inventory->category }}
                            </small>
                            @if($cartItem->inventory->serial_num)
                              <small class="text-muted">
                                <i class="bi bi-upc-scan"></i> {{ $cartItem->inventory->serial_num }}
                              </small>
                            @endif
                            <small class="text-muted">
                              <i class="bi bi-box"></i> Max: {{ $cartItem->inventory->stock_qty + $cartItem->quantity }}
                            </small>
                          </div>
                        </div>
                        <div class="text-end">
                          <form action="{{ route('admin.deployment.updateCart', $cartItem->inventory_id) }}" method="POST" 
                              class="quantity-form">
                            @csrf
                            @method('PUT')
                            <div class="input-group input-group-sm mb-2" style="width: 100px;">
                              <input type="number" 
                                     name="quantity" 
                                     class="form-control" 
                                     value="{{ $cartItem->quantity }}" 
                                     min="1" 
                                     max="{{ $cartItem->inventory->stock_qty + $cartItem->quantity }}"
                                     onchange="this.form.submit()">
                              <span class="input-group-text bg-light">qty</span>
                            </div>
                          </form>
                          <form action="{{ route('admin.deployment.removeFromCart', $cartItem->inventory_id) }}" method="POST" 
      class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                              <i class="bi bi-trash"></i>
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>

              {{-- Cart Summary --}}
              <div class="cart-summary border-top pt-3">
                <h6 class="fw-semibold mb-3">Summary</h6>
                <div class="row g-2 mb-3">
                  <div class="col-6">
                    <div class="stat-box bg-light p-2 rounded text-center">
                      <small class="text-muted d-block">Total Items</small>
                      <strong class="fs-5">{{ $cartItems->sum('quantity') }}</strong>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="stat-box bg-light p-2 rounded text-center">
                      <small class="text-muted d-block">Unique Items</small>
                      <strong class="fs-5">{{ $cartItems->count() }}</strong>
                    </div>
                  </div>
                </div>

                {{-- Clear Cart --}}
                <form action="{{ route('admin.deployment.clearCart') }}" method="POST" class="mb-3">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger w-100" 
                          onclick="return confirm('Clear all items from cart?')">
                    <i class="bi bi-trash me-1"></i> Clear Cart
                  </button>
                </form>

                {{-- Deployment Form --}}
                <form action="{{ route('admin.deployment.deploy') }}" method="POST">
                    @csrf
                    <h6 class="fw-semibold mb-3">Deployment Details</h6>

                    {{-- WAYBILL NUMBER - Optional --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-upc-scan me-1"></i> Waybill No.
                        </label>
                        <input type="text" class="form-control" name="waybill_number" 
                              placeholder="Enter the Waybill Number (optional)">
                    </div>

                    {{-- DEPLOY TO - Dropdown for Contact Persons --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-person-badge me-1"></i> Deploy To *
                        </label>
                        <select class="form-select" name="deployed_to" id="deployToSelect" required>
                            <option value="">-- Select Contact Person --</option>
                            @foreach($contactPersons ?? [] as $contact)
                                <option value="{{ $contact->name }}" 
                                        data-contact="{{ $contact->contact_number }}"
                                        data-address="{{ $contact->address }}"
                                        data-office="{{ $contact->satellite_office }}">
                                    {{ $contact->name }} 
                                    @if($contact->satellite_office)
                                        ({{ $contact->satellite_office }})
                                    @endif
                                </option>
                            @endforeach
                            <option value="new">+ Add New Contact Person</option>
                        </select>
                    </div>

                    {{-- HIDDEN FIELDS - These will store the contact details when an existing contact is selected --}}
                    <input type="hidden" name="contact_number" id="hiddenContactNumber" value="">
                    <input type="hidden" name="address" id="hiddenAddress" value="">
                    <input type="hidden" name="satellite_office" id="hiddenSatelliteOffice" value="">

                    {{-- CONTACT PERSON --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-person-badge me-1"></i> Contact Person
                        </label>
                        <input type="text" class="form-control" name="waybill_number" 
                              placeholder="Enter the Waybill Number (optional)">
                    </div>

                    {{-- VISIBLE FIELDS - For display only --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-telephone me-1"></i> Contact No.
                        </label>
                        <input type="text" class="form-control" id="displayContactNumber" 
                              placeholder="Contact number will appear here" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-geo-alt me-1"></i> Address
                        </label>
                        <textarea class="form-control" id="displayAddress" 
                                  rows="2" placeholder="Address will appear here" readonly></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-building me-1"></i> Satellite Office
                        </label>
                        <input type="text" class="form-control" id="displaySatelliteOffice" 
                              placeholder="Satellite office will appear here" readonly>
                    </div>
                    
                    {{-- NEW CONTACT FIELDS - Only shown when "Add New" is selected --}}
                    <div id="newContactFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">
                                <i class="bi bi-telephone me-1"></i> New Contact No.
                            </label>
                            <input type="text" class="form-control" name="new_contact_number" id="newContactNumber" 
                                  placeholder="Enter Contact Number">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">
                                <i class="bi bi-geo-alt me-1"></i> New Address
                            </label>
                            <textarea class="form-control" name="new_address" id="newAddress" 
                                      rows="2" placeholder="Enter Address"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">
                                <i class="bi bi-building me-1"></i> New Satellite Office
                            </label>
                            <input type="text" class="form-control" name="new_satellite_office" id="newSatelliteOffice" 
                                  placeholder="Enter Satellite Office">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-calendar-date me-1"></i> Deployment Date *
                        </label>
                        <input type="date" class="form-control" name="deployment_date" 
                              value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-chat-left-text me-1"></i> Remarks (Optional)
                        </label>
                        <textarea class="form-control" name="remarks" rows="2" 
                                  placeholder="Add any additional notes..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 py-2 fw-semibold"
                            onclick="return confirm('Deploy {{ $cartItems->sum('quantity') }} item(s)?')">
                        <i class="bi bi-rocket-takeoff me-2"></i> Deploy Items
                    </button>
                </form>
              </div>
            @else
              {{-- Empty Cart --}}
              <div class="text-center py-5">
                <div class="empty-cart-icon mb-3">
                  <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-muted">Your cart is empty</h5>
                <p class="text-muted small">Select items from categories to add to cart</p>
                @if(!session('selected_category'))
                  <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Select a category first to view available items
                  </div>
                @endif
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Custom CSS --}}
<style>
.category-card {
  transition: all 0.3s ease;
  border-radius: 10px;
}

.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  
.category-card:hover {
  transform: translateY(-5px);
}
.category-card.selected {
  border: 2px solid #0d6efd !important;
}
.step-badge {
  width: 32px;
  height: 32px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}
.stat-card {
  padding: 10px 20px;
  border-radius: 8px;
  min-width: 120px;
  text-align: center;
}
.bg-gradient-primary {
  background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
}
.bg-gradient-success {
  background: linear-gradient(135deg, #198754 0%, #146c43 100%);
}
.bg-gradient-info {
  background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
}
.status-badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
}
.quantity-control {
  display: flex;
  align-items: center;
}
.quantity-control .btn {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.quantity-control .form-control {
  width: 60px;
  margin: 0 4px;
}
.cart-item {
  transition: all 0.2s ease;
}
.cart-item:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.empty-cart-icon {
  opacity: 0.5;
}
.sticky-top {
  position: sticky;
  z-index: 1020;
}
.stat-box {
  transition: all 0.3s ease;
}
.stat-box:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

input:read-only, textarea:read-only {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #495057;
    cursor: not-allowed;
}

input:read-only:focus, textarea:read-only:focus {
    border-color: #dee2e6;
    box-shadow: none;
}
</style>

{{-- JavaScript --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deployToSelect = document.getElementById('deployToSelect');
    const contactNumber = document.getElementById('contactNumber');
    const address = document.getElementById('address');
    const satelliteOffice = document.getElementById('satelliteOffice');
    
    function handleContactSelection() {
        const selectedOption = deployToSelect.options[deployToSelect.selectedIndex];
        
        if (deployToSelect.value === 'new') {
            // Enable fields for manual entry
            contactNumber.readOnly = false;
            address.readOnly = false;
            satelliteOffice.readOnly = false;
            
            // Clear fields
            contactNumber.value = '';
            address.value = '';
            satelliteOffice.value = '';
            
            contactNumber.focus();
        } 
        else if (deployToSelect.value === '') {
            // No selection - disable and clear fields
            contactNumber.readOnly = true;
            address.readOnly = true;
            satelliteOffice.readOnly = true;
            contactNumber.value = '';
            address.value = '';
            satelliteOffice.value = '';
        }
        else {
            // Existing contact selected - populate and disable fields
            contactNumber.readOnly = true;
            address.readOnly = true;
            satelliteOffice.readOnly = true;
            
            contactNumber.value = selectedOption.dataset.contact || '';
            address.value = selectedOption.dataset.address || '';
            satelliteOffice.value = selectedOption.dataset.office || '';
        }
    }
    
    deployToSelect.addEventListener('change', handleContactSelection);
    
    if (deployToSelect.value) {
        handleContactSelection();
    }
});
  
document.addEventListener('DOMContentLoaded', function() {
  // Select All Checkbox
  const selectAll = document.getElementById('selectAllComponents');
  const checkboxes = document.querySelectorAll('.component-checkbox');
  
  if (selectAll) {
    selectAll.addEventListener('change', function() {
      checkboxes.forEach(checkbox => {
        if (!checkbox.disabled) {
          checkbox.checked = this.checked;
        }
      });
      updateSelectedCount();
    });
  }
  
  // Update selected count
  function updateSelectedCount() {
    const selected = document.querySelectorAll('.component-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = `${selected} items selected`;
  }
  
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
  });
  
  // Quantity controls
  document.querySelectorAll('.increment').forEach(btn => {
    btn.addEventListener('click', function() {
      const target = document.getElementById(this.dataset.target);
      if (target && target.value < target.max) {
        target.value = parseInt(target.value) + 1;
      }
    });
  });
  
  document.querySelectorAll('.decrement').forEach(btn => {
    btn.addEventListener('click', function() {
      const target = document.getElementById(this.dataset.target);
      if (target && target.value > target.min) {
        target.value = parseInt(target.value) - 1;
      }
    });
  });
  
  // Add single item to cart
  document.querySelectorAll('.add-single-component').forEach(btn => {
    btn.addEventListener('click', function() {
      const itemId = this.dataset.itemId;
      const componentName = this.dataset.component;
      const qtyInput = document.getElementById(`qty-${itemId}`);
      const quantity = qtyInput ? parseInt(qtyInput.value) : 1;
      
      // Add loading state
      const originalHTML = this.innerHTML;
      this.innerHTML = '<i class="bi bi-hourglass-split"></i>';
      this.disabled = true;
      
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
          showNotification('success', `Added ${quantity} × ${componentName} to cart`);
          setTimeout(() => location.reload(), 1000);
        } else {
          showNotification('error', data.message || 'Failed to add item');
          this.innerHTML = originalHTML;
          this.disabled = false;
        }
      })
      .catch(error => {
        showNotification('error', 'Network error occurred');
        this.innerHTML = originalHTML;
        this.disabled = false;
      });
    });
  });
  
  // Reset quantities
  function resetQuantities() {
    document.querySelectorAll('.quantity-input').forEach(input => {
      input.value = 1;
    });
    checkboxes.forEach(checkbox => {
      checkbox.checked = false;
    });
    if (selectAll) selectAll.checked = false;
    updateSelectedCount();
  }
  
  // Notification function
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
  
  // Initialize tooltips
  const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  tooltips.forEach(tooltip => {
    new bootstrap.Tooltip(tooltip);
  });
});

document.addEventListener('DOMContentLoaded', function() {
    const deployToSelect = document.getElementById('deployToSelect');
    const contactNumber = document.getElementById('contactNumber');
    const address = document.getElementById('address');
    const satelliteOffice = document.getElementById('satelliteOffice');
    
    // Function to handle contact selection
    function handleContactSelection() {
        const selectedOption = deployToSelect.options[deployToSelect.selectedIndex];
        
        if (deployToSelect.value === 'new') {
            // Enable all fields for manual entry
            contactNumber.readOnly = false;
            address.readOnly = false;
            satelliteOffice.readOnly = false;
            
            // Clear fields
            contactNumber.value = '';
            address.value = '';
            satelliteOffice.value = '';
            
            // Focus on contact number
            contactNumber.focus();
        } 
        else if (deployToSelect.value === '') {
            // No selection - disable fields
            contactNumber.readOnly = true;
            address.readOnly = true;
            satelliteOffice.readOnly = true;
            
            // Clear fields
            contactNumber.value = '';
            address.value = '';
            satelliteOffice.value = '';
        }
        else {
            // Existing contact selected - populate and disable fields
            contactNumber.readOnly = true;
            address.readOnly = true;
            satelliteOffice.readOnly = true;
            
            // Get data from data attributes
            contactNumber.value = selectedOption.dataset.contact || '';
            address.value = selectedOption.dataset.address || '';
            satelliteOffice.value = selectedOption.dataset.office || '';
        }
    }
    
    // Add event listener
    deployToSelect.addEventListener('change', handleContactSelection);
    
    // Initialize on page load (if there's a pre-selected value)
    if (deployToSelect.value) {
        handleContactSelection();
    }
});
</script>
@endpush
@endsection