@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
            <h1 class="h2 mb-1 fw-bold text-gradient">Suppliers</h1>
            <p class="text-muted mb-0">Manage your inventory suppliers</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary px-3 py-2">
                <i class="bi bi-truck me-1"></i> {{ $suppliers->total() }} Total Suppliers
            </span>
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
                <i class="bi bi-people"></i> Suppliers
            </li>
        </ol>
    </nav>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Controls Section --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <form method="GET" action="{{ route('admin.suppliers') }}" class="row g-2">
                        <div class="col-auto">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="search" 
                                       class="form-control border-start-0" 
                                       name="search" 
                                       placeholder="Search suppliers..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i> Search
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.suppliers') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex justify-content-end align-items-center gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static">
                                <i class="bi bi-funnel me-1"></i> Filter
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.suppliers', ['sort' => 'name']) }}">
                                    <i class="bi bi-sort-alpha-down me-2"></i> Sort by Name
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.suppliers', ['sort' => 'location']) }}">
                                    <i class="bi bi-geo-alt me-2"></i> Sort by Location
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.suppliers') }}">
                                    <i class="bi bi-eye me-2"></i> View All
                                </a></li>
                            </ul>
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-file-earmark-excel me-2"></i> Excel</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-file-earmark-pdf me-2"></i> PDF</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-printer me-2"></i> Print</a></li>
                            </ul>
                        </div>
                        
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                            <i class="bi bi-plus-circle me-1"></i> Add Supplier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Suppliers Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-table me-2"></i> Supplier List
                </h6>
                <small class="text-muted">
                    Showing {{ $suppliers->firstItem() }} to {{ $suppliers->lastItem() }} of {{ $suppliers->total() }} entries
                </small>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">SUPPLIER</th>
                            <th class="border-0">LOCATION</th>
                            <th class="border-0 text-center">CONTACT</th>
                            <th class="border-0 text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr class="hover-shadow">
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="supplier-icon me-3">
                                            <i class="bi bi-truck text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $supplier->name }}</h6>
                                            <small class="text-muted">
                                                Supplier ID: #{{ str_pad($supplier->id, 4, '0', STR_PAD_LEFT) }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    @if($supplier->location)
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-geo-alt text-muted me-2"></i>
                                            <span>{{ $supplier->location }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @if($supplier->contact)
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">{{ $supplier->contact }}</span>
                                            @if($supplier->email)
                                                <small class="text-muted">{{ $supplier->email }}</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No contact</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                title="Edit" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editSupplierModal{{ $supplier->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this supplier?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-truck display-4 text-muted mb-3"></i>
                                        <h5 class="text-muted">No suppliers found</h5>
                                        @if(request('search'))
                                            <p class="text-muted mb-4">No results for "{{ request('search') }}"</p>
                                            <a href="{{ route('admin.suppliers') }}" class="btn btn-outline-secondary">
                                                Clear Search
                                            </a>
                                        @else
                                            <p class="text-muted mb-4">Add your first supplier to get started</p>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                                                <i class="bi bi-plus-circle me-1"></i> Add Supplier
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Footer with Pagination --}}
        @if($suppliers->hasPages())
            <div class="card-footer bg-light py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        Showing {{ $suppliers->firstItem() }} to {{ $suppliers->lastItem() }} of {{ $suppliers->total() }} entries
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            {{ $suppliers->links() }}
                        </ul>
                    </nav>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Add Supplier Modal --}}
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addSupplierModalLabel">
                    <i class="bi bi-plus-circle me-2"></i> Add New Supplier
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label small fw-semibold">
                                <i class="bi bi-building me-1"></i> Supplier Name *
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   placeholder="Enter supplier name" 
                                   required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="location" class="form-label small fw-semibold">
                                <i class="bi bi-geo-alt me-1"></i> Location
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="location" 
                                   name="location" 
                                   placeholder="Enter supplier location">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="contact" class="form-label small fw-semibold">
                                <i class="bi bi-telephone me-1"></i> Contact Number
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="contact" 
                                   name="contact" 
                                   placeholder="Enter contact number">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-semibold">
                                <i class="bi bi-envelope me-1"></i> Email Address
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   placeholder="Enter email address">
                        </div>
                        
                        <div class="col-12">
                            <label for="description" class="form-label small fw-semibold">
                                <i class="bi bi-card-text me-1"></i> Description (Optional)
                            </label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="2" 
                                      placeholder="Enter supplier description or notes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Supplier Modals --}}
@foreach($suppliers as $supplier)
<div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1" aria-labelledby="editSupplierModalLabel{{ $supplier->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editSupplierModalLabel{{ $supplier->id }}">
                    <i class="bi bi-pencil me-2"></i> Edit Supplier
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name{{ $supplier->id }}" class="form-label small fw-semibold">
                                <i class="bi bi-building me-1"></i> Supplier Name *
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name{{ $supplier->id }}" 
                                   name="name" 
                                   value="{{ $supplier->name }}" 
                                   required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="location{{ $supplier->id }}" class="form-label small fw-semibold">
                                <i class="bi bi-geo-alt me-1"></i> Location
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="location{{ $supplier->id }}" 
                                   name="location" 
                                   value="{{ $supplier->location }}">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="contact{{ $supplier->id }}" class="form-label small fw-semibold">
                                <i class="bi bi-telephone me-1"></i> Contact Number
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="contact{{ $supplier->id }}" 
                                   name="contact" 
                                   value="{{ $supplier->contact }}">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email{{ $supplier->id }}" class="form-label small fw-semibold">
                                <i class="bi bi-envelope me-1"></i> Email Address
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email{{ $supplier->id }}" 
                                   name="email" 
                                   value="{{ $supplier->email ?? '' }}" 
                                   placeholder="Enter email address">
                        </div>
                        
                        <div class="col-12">
                            <label for="description{{ $supplier->id }}" class="form-label small fw-semibold">
                                <i class="bi bi-card-text me-1"></i> Description (Optional)
                            </label>
                            <textarea class="form-control" 
                                      id="description{{ $supplier->id }}" 
                                      name="description" 
                                      rows="2" 
                                      placeholder="Enter supplier description or notes">{{ $supplier->description ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Update Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

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
.status-badge {
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
  display: inline-block;
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
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
    
    // Auto-focus search on page load if there's a search query
    @if(request('search'))
        document.querySelector('input[name="search"]')?.focus();
    @endif
    
    // Auto-focus name field in add modal when opened
    const addSupplierModal = document.getElementById('addSupplierModal');
    if (addSupplierModal) {
        addSupplierModal.addEventListener('shown.bs.modal', function () {
            document.getElementById('name').focus();
        });
    }
});
</script>
@endpush
@endsection