@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
    <div>
      <h1 class="h2 mb-1 fw-bold text-gradient">Deployment</h1>
      <p class="text-muted mb-0">Deploy inventory items</p>
    </div>
    {{-- Category Button top right --}}
    <button class="btn btn-primary d-flex align-items-center gap-2 shadow" data-bs-toggle="modal" data-bs-target="#categoryModal">
      <i class="bi bi-grid-3x3-gap-fill"></i>
      <span>Select Category</span>
      @if(session('selected_category'))
        <span class="badge bg-white text-primary ms-1">{{ session('selected_category_name') }}</span>
      @endif
    </button>
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

  {{-- Selected Category Banner (shows when a category is selected) --}}
  @if(session('selected_category'))
    <div class="alert alert-primary d-flex align-items-center mb-4 py-2 px-3 rounded-3 shadow-sm">
      <i class="bi bi-check-circle-fill me-2"></i>
      <span>Showing items from <strong>{{ session('selected_category_name') }}</strong></span>
      <a href="{{ route('admin.deployment.clearCategory') }}" class="btn btn-sm btn-outline-danger ms-auto">
        <i class="bi bi-x-circle me-1"></i> Clear
      </a>
    </div>
  @endif

  {{-- Main Content --}}
  <div class="row g-4">
    {{-- Left Column: Item Selection --}}
    <div class="col-lg-8">

      {{-- Empty state when no category selected --}}
      @if(!session('selected_category'))
        <div class="card border-0 shadow-sm">
          <div class="card-body text-center py-5">
            <i class="bi bi-grid-3x3-gap text-muted" style="font-size: 4rem;"></i>
            <h5 class="text-muted mt-3">No Category Selected</h5>
            <p class="text-muted small mb-4">Click the button above to select a category and view available items</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
              <i class="bi bi-grid-3x3-gap-fill me-2"></i> Browse Categories
            </button>
          </div>
        </div>
      @endif

      {{-- Component Selection Card --}}
      @if(session('selected_category'))
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-gradient-success text-white py-3">
            <div class="d-flex align-items-center">
              <div class="step-badge me-3">
                @php
                  $headerIcons = [
                    'Access Control'  => 'bi-shield-lock',
                    'CCTV'            => 'bi-camera-video',
                    'GPS'             => 'bi-geo-alt',
                    'Wireless Alarm'  => 'bi-bell',
                    'Network'         => 'bi-wifi',
                    'Consumables'     => 'bi-briefcase',
                  ];
                  $headerIcon = $headerIcons[session('selected_category_name')] ?? 'bi-box-seam';
                @endphp
                <i class="bi {{ $headerIcon }}"></i>
              </div>
              <h5 class="mb-0">{{ session('selected_category_name') }}</h5>
              <div class="ms-auto d-flex align-items-center gap-2">
                <span class="badge bg-white text-success">
                  {{ $categoryItems->total() }} items found
                </span>
                <button class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#categoryModal">
                  <i class="bi bi-arrow-left-right me-1"></i> Change
                </button>
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
                      <th width="50"></th>
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
                          <td><code class="text-muted">{{ $item->serial_num ?? 'N/A' }}</code></td>
                          <td class="text-center">
                            <span class="badge 
                              @if($item->stock_qty >= 10) bg-success
                              @elseif($item->stock_qty >= 5) bg-warning text-dark
                              @else bg-danger @endif px-3 py-2">
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
                                         value="1" min="1" max="{{ $item->stock_qty }}"
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
                <button type="submit" form="bulkAddForm" class="btn btn-success">
                  <i class="bi bi-cart-plus me-1"></i> Add Selected to Cart
                </button>
              </div>

              {{-- Pagination --}}
              @if($categoryItems->hasPages())
                <div class="d-flex justify-content-center mt-4">
                  {{ $categoryItems->links() }}
                </div>
              @endif
            @else
              <div class="text-center py-5">
                <i class="bi bi-inboxes fs-1 text-muted"></i>
                <p class="text-muted mt-2">No items found in this category</p>
                @if(request('component_search'))
                  <a href="{{ route('admin.deployment') }}" class="btn btn-sm btn-outline-primary mt-2">Clear Search</a>
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
        <div class="card border-0 shadow-lg">
          <div class="card-header bg-gradient-info text-white py-3">
            <div class="d-flex align-items-center">
              <i class="bi bi-cart-check fs-4 me-2"></i>
              <h5 class="mb-0">Deployment Cart</h5>
              @if($cartItems->count() > 0)
                <span class="badge bg-white text-info ms-auto">{{ $cartItems->sum('quantity') }} items</span>
              @endif
            </div>
          </div>
          
          <div class="card-body">
            @if($cartItems->count() > 0)
              <div class="cart-items mb-4" style="max-height: 300px; overflow-y: auto;">
                @foreach($cartItems as $cartItem)
                  <div class="cart-item card border mb-3">
                    <div class="card-body p-3">
                      <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1 me-3">
                          <h6 class="mb-1 fw-semibold">{{ $cartItem->inventory->component }}</h6>
                          <div class="d-flex flex-wrap gap-2 mb-2">
                            <small class="text-muted"><i class="bi bi-tag"></i> {{ $cartItem->inventory->category }}</small>
                            @if($cartItem->inventory->serial_num)
                              <small class="text-muted"><i class="bi bi-upc-scan"></i> {{ $cartItem->inventory->serial_num }}</small>
                            @endif
                            <small class="text-muted"><i class="bi bi-box"></i> Max: {{ $cartItem->inventory->stock_qty + $cartItem->quantity }}</small>
                          </div>
                        </div>
                        <div class="text-end">
                          <form action="{{ route('admin.deployment.updateCart', $cartItem->inventory_id) }}" method="POST" class="quantity-form">
                            @csrf
                            @method('PUT')
                            <div class="input-group input-group-sm mb-2" style="width: 100px;">
                              <input type="number" name="quantity" class="form-control" 
                                     value="{{ $cartItem->quantity }}" min="1" 
                                     max="{{ $cartItem->inventory->stock_qty + $cartItem->quantity }}"
                                     onchange="this.form.submit()">
                              <span class="input-group-text bg-light">qty</span>
                            </div>
                          </form>
                          <form action="{{ route('admin.deployment.removeFromCart', $cartItem->inventory_id) }}" method="POST" class="d-inline">
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

                <form action="{{ route('admin.deployment.clearCart') }}" method="POST" class="mb-3">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Clear all items from cart?')">
                    <i class="bi bi-trash me-1"></i> Clear Cart
                  </button>
                </form>

                <form action="{{ route('admin.deployment.deploy') }}" method="POST">
                  @csrf
                  <h6 class="fw-semibold mb-3">Deployment Details</h6>

                  <div class="mb-3">
                    <label class="form-label small fw-semibold"><i class="bi bi-upc-scan me-1"></i> Waybill No.</label>
                    <input type="text" class="form-control" name="waybill_number" placeholder="Enter Waybill Number (optional)">
                  </div>

                  <div class="mb-3">
                    <label class="form-label small fw-semibold"><i class="bi bi-person-badge me-1"></i> Deploy To *</label>
                    <select class="form-select" name="deployed_to" id="deployToSelect" required>
                      <option value="">-- Select Contact Person --</option>
                      @foreach($contactPersons ?? [] as $contact)
                        <option value="{{ $contact->name }}" 
                                data-contact="{{ $contact->contact_number }}"
                                data-address="{{ $contact->address }}"
                                data-office="{{ $contact->satellite_office }}">
                          {{ $contact->name }} @if($contact->satellite_office)({{ $contact->satellite_office }})@endif
                        </option>
                      @endforeach
                      <option value="new">+ Add New Contact Person</option>
                    </select>
                  </div>

                  <input type="hidden" name="contact_number" id="hiddenContactNumber" value="">
                  <input type="hidden" name="address" id="hiddenAddress" value="">
                  <input type="hidden" name="satellite_office" id="hiddenSatelliteOffice" value="">

                  <div class="mb-3">
                    <label class="form-label small fw-semibold"><i class="bi bi-telephone me-1"></i> Contact No.</label>
                    <input type="text" class="form-control" id="displayContactNumber" placeholder="Auto-filled from selection" readonly>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small fw-semibold"><i class="bi bi-geo-alt me-1"></i> Address</label>
                    <textarea class="form-control" id="displayAddress" rows="2" placeholder="Auto-filled from selection" readonly></textarea>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small fw-semibold"><i class="bi bi-building me-1"></i> Satellite Office</label>
                    <input type="text" class="form-control" id="displaySatelliteOffice" placeholder="Auto-filled from selection" readonly>
                  </div>

                  <div id="newContactFields" style="display: none;">
                    <div class="mb-3">
                      <label class="form-label small fw-semibold"><i class="bi bi-person me-1"></i> New Contact Name *</label>
                      <input type="text" class="form-control" name="new_contact_name" placeholder="Enter Name">
                    </div>
                    <div class="mb-3">
                      <label class="form-label small fw-semibold"><i class="bi bi-telephone me-1"></i> New Contact No.</label>
                      <input type="text" class="form-control" name="new_contact_number" id="newContactNumber" placeholder="Enter Contact Number">
                    </div>
                    <div class="mb-3">
                      <label class="form-label small fw-semibold"><i class="bi bi-geo-alt me-1"></i> New Address</label>
                      <textarea class="form-control" name="new_address" id="newAddress" rows="2" placeholder="Enter Address"></textarea>
                    </div>
                    <div class="mb-3">
                      <label class="form-label small fw-semibold"><i class="bi bi-building me-1"></i> New Satellite Office</label>
                      <input type="text" class="form-control" name="new_satellite_office" id="newSatelliteOffice" placeholder="Enter Satellite Office">
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small fw-semibold"><i class="bi bi-calendar-date me-1"></i> Deployment Date *</label>
                    <input type="date" class="form-control" name="deployment_date" value="{{ date('Y-m-d') }}" required>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small fw-semibold"><i class="bi bi-chat-left-text me-1"></i> Remarks (Optional)</label>
                    <textarea class="form-control" name="remarks" rows="2" placeholder="Add any additional notes..."></textarea>
                  </div>

                  <button type="submit" class="btn btn-success w-100 py-2 fw-semibold"
                          onclick="return confirm('Deploy {{ $cartItems->sum('quantity') }} item(s)?')">
                    <i class="bi bi-rocket-takeoff me-2"></i> Deploy Items
                  </button>
                </form>
              </div>
            @else
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

{{-- ===== CATEGORY MODAL ===== --}}
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-gradient-primary text-white">
        <div class="d-flex align-items-center gap-2">
          <div class="step-badge"><i class="bi bi-grid-3x3-gap-fill"></i></div>
          <h5 class="modal-title mb-0" id="categoryModalLabel">Select a Category</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
          @forelse($categories as $category)
            <div class="col-md-4 col-sm-6">
              <div class="category-card card h-100 border-0 hover-lift 
                @if(session('selected_category') == $category->id) selected shadow @else shadow-sm @endif">
                <div class="card-body d-flex flex-column align-items-center text-center p-4">

                  {{-- Icon --}}
                  <div class="category-icon mb-3">
                    @php
                      $icons = [
                        'Access Control' => 'bi-shield-lock',
                        'CCTV'           => 'bi-camera-video',
                        'GPS'            => 'bi-geo-alt',
                        'Wireless Alarm' => 'bi-bell',
                        'Network'        => 'bi-wifi',
                        'Consumables'    => 'bi-briefcase',
                      ];
                      $icon = $icons[$category->name] ?? 'bi-box-seam';
                    @endphp
                    <i class="bi {{ $icon }} fs-1 
                      @if($category->available_count > 0) text-primary @else text-muted @endif"></i>
                  </div>

                  {{-- Name --}}
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
            <div class="col-12 text-center py-5">
              <i class="bi bi-folder-x fs-1 text-muted"></i>
              <p class="text-muted mt-2">No categories found</p>
            </div>
          @endforelse
        </div>
      </div>
      <div class="modal-footer bg-light">
        <small class="text-muted me-auto"><i class="bi bi-info-circle me-1"></i> Select a category to view available items</small>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

{{-- Custom CSS --}}
<style>
.text-gradient {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.category-card {
  transition: all 0.3s ease;
  border-radius: 10px;
  cursor: pointer;
}
.category-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
}
.category-card.selected {
  border: 2px solid #0d6efd !important;
  background: #f0f5ff;
}
.step-badge {
  width: 36px;
  height: 36px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1rem;
}
.bg-gradient-primary { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); }
.bg-gradient-success { background: linear-gradient(135deg, #198754 0%, #146c43 100%); }
.bg-gradient-info    { background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%); }
.status-badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
  color: #fff;
}
.quantity-control { display: flex; align-items: center; }
.quantity-control .btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; }
.quantity-control .form-control { width: 60px; margin: 0 4px; }
.cart-item { transition: all 0.2s ease; }
.cart-item:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.empty-cart-icon { opacity: 0.5; }
.sticky-top { position: sticky; z-index: 1020; }
.stat-box { transition: all 0.3s ease; }
.stat-box:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
input[readonly], textarea[readonly] {
  background-color: #f8f9fa;
  border-color: #dee2e6;
  color: #495057;
  cursor: not-allowed;
}
input[readonly]:focus, textarea[readonly]:focus {
  border-color: #dee2e6;
  box-shadow: none;
}
</style>

{{-- JavaScript --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

  // ── Deploy To select: auto-fill contact details ──
  const deployToSelect = document.getElementById('deployToSelect');
  if (deployToSelect) {
    deployToSelect.addEventListener('change', function() {
      const opt = this.options[this.selectedIndex];
      const newFields = document.getElementById('newContactFields');

      if (this.value === 'new') {
        newFields.style.display = 'block';
        document.getElementById('displayContactNumber').value = '';
        document.getElementById('displayAddress').value = '';
        document.getElementById('displaySatelliteOffice').value = '';
      } else if (this.value === '') {
        newFields.style.display = 'none';
        document.getElementById('displayContactNumber').value = '';
        document.getElementById('displayAddress').value = '';
        document.getElementById('displaySatelliteOffice').value = '';
        document.getElementById('hiddenContactNumber').value = '';
        document.getElementById('hiddenAddress').value = '';
        document.getElementById('hiddenSatelliteOffice').value = '';
      } else {
        newFields.style.display = 'none';
        const contact = opt.dataset.contact || '';
        const address = opt.dataset.address || '';
        const office  = opt.dataset.office  || '';
        document.getElementById('displayContactNumber').value  = contact;
        document.getElementById('displayAddress').value        = address;
        document.getElementById('displaySatelliteOffice').value = office;
        document.getElementById('hiddenContactNumber').value   = contact;
        document.getElementById('hiddenAddress').value         = address;
        document.getElementById('hiddenSatelliteOffice').value = office;
      }
    });
  }

  // ── Checkboxes: selected count ──
  const checkboxes = document.querySelectorAll('.component-checkbox');
  const countBadge = document.getElementById('selectedCount');

  function updateSelectedCount() {
    const n = document.querySelectorAll('.component-checkbox:checked').length;
    if (countBadge) countBadge.textContent = `${n} items selected`;
  }
  checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedCount));

  // ── Quantity +/- buttons ──
  document.querySelectorAll('.increment').forEach(btn => {
    btn.addEventListener('click', function() {
      const t = document.getElementById(this.dataset.target);
      if (t && parseInt(t.value) < parseInt(t.max)) t.value = parseInt(t.value) + 1;
    });
  });
  document.querySelectorAll('.decrement').forEach(btn => {
    btn.addEventListener('click', function() {
      const t = document.getElementById(this.dataset.target);
      if (t && parseInt(t.value) > parseInt(t.min)) t.value = parseInt(t.value) - 1;
    });
  });

  // ── Reset quantities ──
  window.resetQuantities = function() {
    document.querySelectorAll('.quantity-input').forEach(i => i.value = 1);
    checkboxes.forEach(cb => cb.checked = false);
    updateSelectedCount();
  };

  // ── Notification helper ──
  window.showNotification = function(type, message) {
    const el = document.createElement('div');
    el.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    el.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:300px;';
    el.innerHTML = `<div class="d-flex align-items-center">
      <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill'} me-2"></i>
      <div>${message}</div>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>`;
    document.body.appendChild(el);
    setTimeout(() => el.parentNode && el.remove(), 5000);
  };

  // ── Tooltips ──
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
});
</script>
@endpush
@endsection