@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
            <h1 class="h2 mb-1 fw-bold text-gradient">Deployment Reports</h1>
            <p class="text-muted mb-0">View and manage deployment reports</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary px-3 py-2">
                <i class="bi bi-file-earmark-text me-1"></i> 0 Reports Generated
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
                <i class="bi bi-file-earmark-text"></i> Reports
            </li>
        </ol>
    </nav>

    {{-- Controls Section --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <form method="GET" action="{{ route('admin.reports') }}" class="row g-2">
                        <div class="col-auto">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="search" 
                                       class="form-control border-start-0" 
                                       name="search" 
                                       placeholder="Search reports..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i> Search
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary">
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
                                <i class="bi bi-funnel me-1"></i> Filter by Status
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">
                                    <span class="badge bg-success me-2">●</span> Completed
                                </a></li>
                                <li><a class="dropdown-item" href="#">
                                    <span class="badge bg-primary me-2">●</span> In Progress
                                </a></li>
                                <li><a class="dropdown-item" href="#">
                                    <span class="badge bg-warning me-2">●</span> Pending
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reports Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-table me-2"></i> Deployment Reports
                </h6>
                <small class="text-muted">
                    Showing 0 reports
                </small>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">REPORT TITLE</th>
                            <th class="border-0 text-center">TYPE</th>
                            <th class="border-0 text-center">DATE GENERATED</th>
                            <th class="border-0 text-center">PERIOD</th>
                            <th class="border-0 text-center">STATUS</th>
                            <th class="border-0 text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-file-earmark-text display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">No reports generated yet</h5>
                                    <p class="text-muted mb-4">Reports will appear here once generated</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Footer with Pagination --}}
        <div class="card-footer bg-light py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Showing 0 to 0 of 0 entries
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled">
                            <span class="page-link">&laquo;</span>
                        </li>
                        <li class="page-item active"><span class="page-link">1</span></li>
                        <li class="page-item disabled">
                            <span class="page-link">&raquo;</span>
                        </li>
                    </ul>
                </nav>
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

.hover-shadow:hover {
    background-color: rgba(0, 0, 0, 0.02);
    transition: all 0.2s ease;
}

.report-icon {
    width: 36px;
    height: 36px;
    background: rgba(13, 110, 253, 0.1);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.breadcrumb {
    background-color: #f8f9fa !important;
    border-radius: 8px;
}

.card {
    border-radius: 10px;
    overflow: hidden;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    color: #6c757d;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    border-bottom: 1px solid #f0f0f0;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.badge {
    border-radius: 6px;
    font-weight: 500;
}

.empty-state {
    padding: 3rem 1rem;
}

/* Fix for dropdown z-index */
.dropdown {
    z-index: 1000 !important;
}

.dropdown-menu {
    z-index: 1001 !important;
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
      
      // Fix dropdown positioning
      const dropdowns = document.querySelectorAll('.dropdown-toggle');
      dropdowns.forEach(dropdown => {
          dropdown.addEventListener('click', function() {
              // Ensure dropdown menu is properly positioned
              const menu = this.nextElementSibling;
              if (menu) {
                  menu.style.zIndex = '9999';
              }
          });
      });
  });
</script>
@endpush
@endsection