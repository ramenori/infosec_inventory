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
                Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }} reports
            </small>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">CATEGORY</th>
                        <th class="border-0 text-center">COMPONENT</th>
                        <th class="border-0 text-center">QUANTITY</th>
                        <th class="border-0 text-center">DEPLOYED TO</th>
                        <th class="border-0 text-center">DATE DEPLOYED</th>
                        <th class="border-0 text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $deployment)
                        @foreach($deployment->items as $item)
                            <tr class="hover-shadow">
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="item-icon me-2">
                                            @php
                                                $categoryIcons = [
                                                    'Computers' => 'bi-laptop',
                                                    'Electronics' => 'bi-cpu',
                                                    'Furniture' => 'bi-chair',
                                                    'Office Supplies' => 'bi-briefcase',
                                                    'Network' => 'bi-wifi',
                                                    'Other' => 'bi-box'
                                                ];
                                                // Get category from inventory relationship
                                                $category = $item->inventory->category ?? 'Other';
                                                $icon = $categoryIcons[$category] ?? 'bi-grid';
                                            @endphp
                                            <i class="bi {{ $icon }} text-primary"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block">{{ $category }}</strong>
                                            <small class="text-muted">{{ $deployment->reference_number }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <div>
                                        <strong class="d-block">{{ $item->component }}</strong>
                                        @if($item->inventory)
                                            <small class="text-muted">{{ $item->inventory->brand ?? 'N/A' }}</small>
                                            <br>
                                            <small class="text-muted">
                                                <code>{{ $item->inventory->serial_num ?? 'No Serial' }}</code>
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-primary px-3 py-2">{{ $item->quantity }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <div>
                                        <strong class="d-block">{{ $deployment->deployed_to }}</strong>
                                        @if($deployment->department)
                                            <small class="text-muted">Dept: {{ $deployment->department }}</small>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ Str::limit($deployment->remarks, 30) ?? 'No remarks' }}</small>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <div>
                                        <strong class="d-block">{{ $deployment->deployment_date->format('M d, Y') }}</strong>
                                        <small class="text-muted">{{ $deployment->created_at->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="tooltip" data-bs-placement="top" 
                                                title="View Details"
                                                onclick="viewReport({{ $deployment->id }})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        
                                
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-file-earmark-text display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">No deployment reports found</h5>
                                    <p class="text-muted mb-4">Deploy some items first to see reports here</p>
                                    <a href="{{ route('admin.deployment') }}" class="btn btn-primary">
                                        <i class="bi bi-truck me-1"></i> Go to Deployment
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Footer with Pagination --}}
    @if($reports->hasPages())
        <div class="card-footer bg-light py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} entries
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{ $reports->links() }}
                    </ul>
                </nav>
            </div>
        </div>
    @endif
</div>
    
    {{-- Footer with Pagination --}}
    @if($reports->hasPages())
        <div class="card-footer bg-light py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} entries
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{ $reports->links() }}
                    </ul>
                </nav>
            </div>
        </div>
    @endif
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