@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-3">
  {{-- Header with Restored Gradient --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h2 mb-1 fw-bold text-gradient">Deployment</h1>
      <p class="text-muted small mb-0">Select categories, pick components, and dispatch items to destinations</p>
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

  {{-- Toast Notifications --}}
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-lg rounded-3 border-0 py-2.5 px-3" role="alert" style="min-width: 320px;">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div class="flex-grow-1 small fw-semibold">{{ session('success') }}</div>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-lg rounded-3 border-0 py-2.5 px-3" role="alert" style="min-width: 320px;">
        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
        <div class="flex-grow-1 small fw-semibold">{{ session('error') }}</div>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif
  </div>

  {{-- Selected Category Banner --}}
  @if(session('selected_category'))
    <div class="alert alert-primary bg-primary-subtle border-primary-subtle d-flex align-items-center mb-4 py-2.5 px-3 rounded-3 shadow-sm">
      <div class="d-flex align-items-center gap-2">
        <span class="spinner-grow spinner-grow-sm text-primary" role="status"></span>
        <span class="text-primary-emphasis fw-medium">Active Category: <strong>{{ session('selected_category_name') }}</strong></span>
      </div>
      <a href="{{ route('admin.deployment.clearCategory') }}" class="btn btn-sm btn-outline-primary rounded-pill ms-auto px-3">
        <i class="bi bi-arrow-left-right me-1"></i> Switch Category
      </a>
    </div>
  @endif

  {{-- Main Layout --}}
  <div class="row g-4">
    {{-- Left Column: Item Selection --}}
    <div class="col-lg-8">

      {{-- Categories Grid (when no category selected) --}}
      @if(!session('selected_category'))
        <div class="mb-3">
          <h6 class="fw-bold text-uppercase text-muted tracking-wider small">Select Category to Deploy From</h6>
        </div>
        <div class="row g-3">
          @forelse($categories as $category)
            <div class="col-md-6 col-lg-4">
              <div class="category-card card h-100 border shadow-sm rounded-4 hover-lift">
                <div class="card-body p-4 d-flex flex-column align-items-center text-center">
                  @php
                    $iconConfig = [
                      'Access Control' => ['icon' => 'bi-shield-lock-fill', 'bg' => 'bg-primary-subtle', 'color' => 'text-primary'],
                      'CCTV'           => ['icon' => 'bi-camera-video-fill', 'bg' => 'bg-info-subtle', 'color' => 'text-info'],
                      'GPS'            => ['icon' => 'bi-geo-alt-fill', 'bg' => 'bg-danger-subtle', 'color' => 'text-danger'],
                      'Wireless Alarm' => ['icon' => 'bi-bell-fill', 'bg' => 'bg-warning-subtle', 'color' => 'text-warning-emphasis'],
                      'Network'        => ['icon' => 'bi-wifi', 'bg' => 'bg-success-subtle', 'color' => 'text-success'],
                      'Consumables'    => ['icon' => 'bi-box-seam-fill', 'bg' => 'bg-secondary-subtle', 'color' => 'text-secondary'],
                    ];
                    $cfg = $iconConfig[$category->name] ?? ['icon' => 'bi-boxes', 'bg' => 'bg-light', 'color' => 'text-primary'];
                  @endphp

                  <div class="icon-avatar mb-3 {{ $cfg['bg'] }} {{ $cfg['color'] }} rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi {{ $cfg['icon'] }} fs-3"></i>
                  </div>

                  <h6 class="fw-bold text-dark mb-1">{{ $category->name }}</h6>
                  <p class="text-muted small mb-3">{{ $category->available_count }} available unit(s)</p>

                  <div class="d-flex w-100 justify-content-between align-items-center bg-light p-2.5 px-3 rounded-3 mb-3 small">
                    <span class="text-muted">Total: <strong>{{ $category->items_count }}</strong></span>
                    <span class="text-success fw-semibold">In Stock: {{ $category->available_count }}</span>
                  </div>

                  @if($category->available_count > 0)
                    <form action="{{ route('admin.deployment.selectCategory') }}" method="POST" class="w-100 mt-auto">
                      @csrf
                      <input type="hidden" name="category_id" value="{{ $category->id }}">
                      <input type="hidden" name="category_name" value="{{ $category->name }}">
                      <input type="hidden" name="available_count" value="{{ $category->available_count }}">
                      <button type="submit" class="btn btn-primary btn-sm w-100 rounded-3 py-2 fw-semibold">
                        Select Category <i class="bi bi-arrow-right ms-1"></i>
                      </button>
                    </form>
                  @else
                    <button type="button" class="btn btn-light text-muted btn-sm w-100 rounded-3 py-2 mt-auto" disabled>
                      <i class="bi bi-slash-circle me-1"></i> Out of Stock
                    </button>
                  @endif
                </div>
              </div>
            </div>
          @empty
            <div class="col-12">
              <div class="p-5 text-center bg-white rounded-4 border shadow-sm">
                <i class="bi bi-folder-x display-4 text-muted mb-2"></i>
                <h6 class="fw-bold text-dark">No Categories Available</h6>
                <p class="text-muted small mb-0">Create inventory categories first before performing deployments.</p>
              </div>
            </div>
          @endforelse
        </div>
      @endif

      {{-- Component Selection Table Card --}}
      @if(session('selected_category'))
        <div class="card border shadow-sm rounded-4 overflow-hidden bg-white">
          <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
            <div>
              <h6 class="mb-0 fw-bold text-dark">{{ session('selected_category_name') }} Components</h6>
              <small class="text-muted">Choose items to add to the deployment cart</small>
            </div>
            <a href="{{ route('admin.deployment.clearCategory') }}" class="btn btn-light btn-sm rounded-pill px-3 border">
              <i class="bi bi-x-circle me-1"></i> Cancel
            </a>
          </div>

          <div class="card-body p-4">
            {{-- Search Bar --}}
            <div class="row g-2 mb-4">
              <div class="col-md-9">
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                  </span>
                  <input type="search" 
                         class="form-control border-start-0 bg-light" 
                         placeholder="Search component or brand..."
                         name="component_search"
                         value="{{ request('component_search') }}"
                         form="searchForm">
                </div>
              </div>
              <div class="col-md-3">
                <form id="searchForm" method="GET" action="{{ route('admin.deployment') }}" class="d-flex gap-2">
                  <button type="submit" class="btn btn-dark w-100">
                    Search
                  </button>
                  @if(request('component_search'))
                    <a href="{{ route('admin.deployment') }}" class="btn btn-outline-secondary">
                      <i class="bi bi-x"></i>
                    </a>
                  @endif
                </form>
              </div>
            </div>

            {{-- Components Table --}}
            @if($categoryItems->count() > 0)
              <div class="table-responsive rounded-3 border">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th width="40" class="ps-3"></th>
                      <th>COMPONENT</th>
                      <th>BRAND</th>
                      <th class="text-center">STOCK</th>
                      <th class="text-center pe-3">STATUS</th>
                    </tr>
                  </thead>
                  <tbody>
                    <form id="bulkAddForm" action="{{ route('admin.deployment.bulkAddToCart') }}" method="POST">
                      @csrf
                      <input type="hidden" name="category_id" value="{{ session('selected_category') }}">
                      
                      @foreach($categoryItems as $item)
                        <tr class="@if($item->stock_qty == 0 || $item->status !== 'Available') bg-light opacity-75 @endif">
                          <td class="ps-3">
                            @if($item->stock_qty > 0 && $item->status === 'Available')
                              <input class="form-check-input component-checkbox" 
                                     type="checkbox" 
                                     name="component_ids[]" 
                                     value="{{ $item->id }}">
                            @endif
                          </td>
                          <td>
                            <div class="fw-semibold text-dark">{{ $item->component }}</div>
                            <small class="text-muted d-block" style="font-size: 11px;">{{ $item->description ?? 'No description' }}</small>
                          </td>
                          <td>
                            <span class="badge bg-light text-dark border fw-normal">{{ $item->brand ?? 'N/A' }}</span>
                          </td>
                          <td class="text-center">
                            <span class="fw-bold text-dark">{{ $item->stock_qty }}</span>
                          </td>
                          <td class="text-center pe-3">
                            @if($item->status === 'Available')
                              <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">Available</span>
                            @else
                              <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2.5 py-1">{{ $item->status }}</span>
                            @endif
                          </td>
                        </tr>
                      @endforeach
                    </form>
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded-3 border">
                <span id="selectedCount" class="small fw-bold text-muted">0 items selected</span>
                <button type="submit" form="bulkAddForm" class="btn btn-primary btn-sm px-4 rounded-3 fw-semibold">
                  <i class="bi bi-cart-plus me-1.5"></i> Add Selected to Cart
                </button>
              </div>

              @if($categoryItems->hasPages())
                <div class="d-flex justify-content-center mt-3">
                  {{ $categoryItems->links() }}
                </div>
              @endif
            @else
              <div class="p-5 text-center">
                <i class="bi bi-inbox display-6 text-muted mb-2 d-block"></i>
                <span class="fw-bold text-dark d-block">No Items Found</span>
                <small class="text-muted">There are no available components found matching your selection.</small>
              </div>
            @endif
          </div>
        </div>
      @endif
    </div>

    {{-- Right Column: Deployment Cart --}}
    <div class="col-lg-4">
      <div class="sticky-top" style="top: 20px;">
        <div class="card border shadow-sm rounded-4 overflow-hidden bg-white">
          <div class="card-header bg-dark text-white py-3 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-cart-check fs-5"></i>
              <h6 class="mb-0 fw-bold">Deployment Cart</h6>
            </div>
            @if($cartItems->count() > 0)
              <span class="badge bg-primary rounded-pill px-2.5 py-1.5">{{ $cartItems->sum('quantity') }} items</span>
            @endif
          </div>

          <div class="card-body p-4">
            @if($cartItems->count() > 0)
              {{-- Cart List --}}
              <div class="cart-items mb-3" style="max-height: 280px; overflow-y: auto;">
                @foreach($cartItems as $cartItem)
                  <div class="card border rounded-3 p-3 mb-2 bg-white shadow-2xs">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div class="lh-1">
                        <span class="fw-bold text-dark small d-block mb-1">{{ $cartItem->inventory->component }}</span>
                        <small class="text-muted" style="font-size: 11px;">Max: {{ $cartItem->inventory->stock_qty + $cartItem->quantity }}</small>
                      </div>
                      <div class="d-flex align-items-center gap-1">
                        <form action="{{ route('admin.deployment.updateCart', $cartItem->inventory_id) }}" method="POST">
                          @csrf
                          @method('PUT')
                          <input type="number" name="quantity" class="form-control form-control-sm text-center fw-bold" 
                                 value="{{ $cartItem->quantity }}" min="1" 
                                 max="{{ $cartItem->inventory->stock_qty + $cartItem->quantity }}"
                                 style="width: 55px;"
                                 onchange="this.form.submit()">
                        </form>
                        <form action="{{ route('admin.deployment.removeFromCart', $cartItem->inventory_id) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-light text-danger border-0 p-1.5 rounded-2">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      </div>
                    </div>

                    {{-- Dynamic Serial inputs --}}
                    <div class="border-top pt-2 mt-2">
                      <small class="text-muted fw-semibold d-block mb-1" style="font-size: 10px;">ENTER SERIAL NUMBERS:</small>
                      @for($i = 0; $i < $cartItem->quantity; $i++)
                        <div class="input-group input-group-sm mb-1">
                          <span class="input-group-text bg-light text-muted" style="font-size: 10px;">#{{ $i + 1 }}</span>
                          <input type="text" 
                                 name="serials[{{ $cartItem->inventory_id }}][]" 
                                 class="form-control form-control-sm" 
                                 placeholder="Serial number"
                                 form="deploymentSubmitForm">
                        </div>
                      @endfor
                    </div>
                  </div>
                @endforeach
              </div>

              {{-- Cart Clear & Dispatch Form --}}
              <div class="border-top pt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <span class="text-muted small">Cart Subtotal:</span>
                  <span class="fw-bold text-dark">{{ $cartItems->sum('quantity') }} Units</span>
                </div>

                <form action="{{ route('admin.deployment.clearCart') }}" method="POST" class="mb-3">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-light text-danger border w-100 btn-sm rounded-2" onclick="return confirm('Clear cart?')">
                    <i class="bi bi-trash me-1"></i> Empty Cart
                  </button>
                </form>

                <form id="deploymentSubmitForm" action="{{ route('admin.deployment.deploy') }}" method="POST">
                  @csrf
                  <div class="mb-2.5">
                    <label class="form-label small fw-semibold text-muted mb-1">Waybill No. (Optional)</label>
                    <input type="text" class="form-control form-control-sm" name="waybill_number" placeholder="e.g. WB-98402">
                  </div>

                  <div class="mb-2.5">
                    <label class="form-label small fw-semibold text-muted mb-1">Deploy To Target *</label>
                    <select class="form-select form-select-sm" name="contact_person_id" id="contactPersonSelect" required>
                      <option value="">Select recipient...</option>
                      @foreach($contactPersons as $contactPerson)
                        <option value="{{ $contactPerson->id }}"
                                data-name="{{ $contactPerson->name }}"
                                {{ old('contact_person_id') == $contactPerson->id ? 'selected' : '' }}>
                          {{ $contactPerson->name }}
                        </option>
                      @endforeach
                    </select>
                    <input type="hidden" name="deployed_to" id="deployedToInput" value="{{ old('deployed_to') }}">
                  </div>

                  <div class="mb-2.5">
                    <label class="form-label small fw-semibold text-muted mb-1">Dispatch Date *</label>
                    <input type="date" class="form-control form-control-sm" name="deployment_date" value="{{ old('deployment_date', date('Y-m-d')) }}" required>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Remarks</label>
                    <textarea class="form-control form-control-sm" name="remarks" rows="2" placeholder="Deployment notes..."></textarea>
                  </div>

                  <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold shadow-sm rounded-3"
                          onclick="return confirm('Confirm deployment of {{ $cartItems->sum('quantity') }} unit(s)?')">
                    <i class="bi bi-rocket-takeoff me-1.5"></i> Confirm Deployment
                  </button>
                </form>
              </div>
            @else
              {{-- Empty Cart State --}}
              <div class="p-4 text-center">
                <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                  <i class="bi bi-cart-x display-6 text-muted"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Cart is Empty</h6>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Styling overrides --}}
<style>
.text-gradient {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}
.category-card {
  transition: all 0.2s ease-in-out;
}
.category-card:hover {
  border-color: #0d6efd !important;
  transform: translateY(-3px);
}
.hover-lift {
  transition: transform 0.2s ease;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const checkboxes = document.querySelectorAll('.component-checkbox');
  const countBadge = document.getElementById('selectedCount');

  function updateSelectedCount() {
    const n = document.querySelectorAll('.component-checkbox:checked').length;
    if (countBadge) countBadge.textContent = `${n} items selected`;
  }
  checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedCount));

  const contactPersonSelect = document.getElementById('contactPersonSelect');
  const deployedToInput = document.getElementById('deployedToInput');

  function populateContactDetails() {
    const selectedOption = contactPersonSelect?.selectedOptions[0];
    if (!selectedOption || !deployedToInput) return;
    deployedToInput.value = selectedOption.dataset.name || '';
  }

  contactPersonSelect?.addEventListener('change', populateContactDetails);
  populateContactDetails();
});
</script>
@endpush
@endsection