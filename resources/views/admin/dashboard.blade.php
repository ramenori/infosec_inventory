@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
    <div>
      <h1 class="h2 mb-1 fw-bold text-gradient">Dashboard Overview</h1>
      <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name ?? 'Admin' }}! Here's what's happening with your inventory.</p>
    </div>
    
    <!-- Action Buttons -->
    <div class="d-flex gap-2">
      <div class="dropdown">
        <button class="btn btn-outline-dark rounded-3 px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-funnel me-2"></i>Filter
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
          <li><h6 class="dropdown-header">Time Period</h6></li>
          <li><a class="dropdown-item" href="#"><i class="bi bi-calendar-week me-2"></i>This Week</a></li>
          <li><a class="dropdown-item" href="#"><i class="bi bi-calendar-month me-2"></i>This Month</a></li>
          <li><a class="dropdown-item" href="#"><i class="bi bi-calendar-range me-2"></i>Last 90 Days</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="#"><i class="bi bi-calendar me-2"></i>Custom Range</a></li>
        </ul>
      </div>
      
      <div class="dropdown">
        <button class="btn btn-primary rounded-3 px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-download me-2"></i>Export
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
          <li><h6 class="dropdown-header">Export Format</h6></li>
          <li><a class="dropdown-item" href="#"><i class="bi bi-file-earmark-excel text-success me-2"></i>Export as Excel</a></li>
          <li><a class="dropdown-item" href="#"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Export as PDF</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="#"><i class="bi bi-printer me-2"></i>Print Report</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="row g-4 mb-4">
    <!-- Most Item Deployed -->
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm h-100 card-hover" style="border-left: 4px solid #667eea;">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
              <i class="bi bi-rocket-fill text-primary fs-4"></i>
            </div>
            <span class="badge bg-primary bg-opacity-20 text-primary rounded-pill">+12%</span>
          </div>
          <h3 class="mb-2 fw-bold">-</h3>
          <p class="text-muted mb-0">Most Deployed Item</p>
          <div class="mt-3">
            <small class="text-success"><i class="bi bi-arrow-up-right me-1"></i> Top performing item</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Supplier -->
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm h-100 card-hover" style="border-left: 4px solid #10b981;">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="bg-success bg-opacity-10 rounded-3 p-3">
              <i class="bi bi-truck text-success fs-4"></i>
            </div>
            <span class="badge bg-success bg-opacity-20 text-success rounded-pill">Reliable</span>
          </div>
          <h3 class="mb-2 fw-bold">-</h3>
          <p class="text-muted mb-0">Top Supplier</p>
          <div class="mt-3">
            <small class="text-info"><i class="bi bi-star-fill me-1"></i> Highest rated supplier</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Stock QTY -->
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm h-100 card-hover" style="border-left: 4px solid #f59e0b;">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
              <i class="bi bi-box-seam text-warning fs-4"></i>
            </div>
            <span class="badge bg-warning bg-opacity-20 text-warning rounded-pill">Inventory</span>
          </div>
          <h3 class="mb-2 fw-bold">-</h3>
          <p class="text-muted mb-0">Total Stock Quantity</p>
          <div class="mt-3">
            <small class="text-warning"><i class="bi bi-bar-chart me-1"></i> All items in stock</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Items Deployed This Month -->
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm h-100 card-hover" style="border-left: 4px solid #3b82f6;">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="bg-info bg-opacity-10 rounded-3 p-3">
              <i class="bi bi-calendar-check text-info fs-4"></i>
            </div>
            <span class="badge bg-info bg-opacity-20 text-info rounded-pill">This Month</span>
          </div>
          <h3 class="mb-2 fw-bold">-</h3>
          <p class="text-muted mb-0">Items Deployed</p>
          <div class="mt-3">
            <small class="text-primary"><i class="bi bi-arrow-up-circle me-1"></i> Monthly deployment count</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="row g-4 mb-4">
    <!-- Analytics Chart -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 py-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-0 fw-bold">Analytics Overview</h5>
              <p class="text-muted mb-0 small">Inventory trends and performance metrics</p>
            </div>
            <div class="dropdown">
              <button class="btn btn-sm btn-outline-secondary rounded-2" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View Details</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i>Download Data</a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="position-relative" style="height: 300px;">
            <!-- Chart Placeholder with Modern Design -->
            <div class="d-flex flex-column justify-content-center align-items-center h-100">
              <div class="mb-3">
                <i class="bi bi-bar-chart-line text-primary opacity-25" style="font-size: 4rem;"></i>
              </div>
              <h4 class="text-muted mb-2">Analytics Dashboard</h4>
              <p class="text-muted text-center mb-4">Visual charts and graphs will appear here<br>showing inventory trends and metrics.</p>
              <div class="d-flex gap-3">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                  <i class="bi bi-circle-fill me-1"></i> Deployment Trends
                </span>
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                  <i class="bi bi-circle-fill me-1"></i> Stock Levels
                </span>
                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2">
                  <i class="bi bi-circle-fill me-1"></i> Supplier Performance
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Stats & Activity -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 py-3">
          <h5 class="mb-0 fw-bold">Recent Activity</h5>
        </div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            <a href="#" class="list-group-item list-group-item-action border-0 py-3">
              <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                  <i class="bi bi-plus-circle text-primary"></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-1">New item added</h6>
                  <small class="text-muted">Laptop Dell XPS 15 added to inventory</small>
                </div>
                <small class="text-muted">2 min ago</small>
              </div>
            </a>
            <a href="#" class="list-group-item list-group-item-action border-0 py-3">
              <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 rounded-3 p-2 me-3">
                  <i class="bi bi-exclamation-triangle text-warning"></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-1">Low stock alert</h6>
                  <small class="text-muted">Mouse Logitech MX Master 3 low stock</small>
                </div>
                <small class="text-muted">1 hour ago</small>
              </div>
            </a>
            <a href="#" class="list-group-item list-group-item-action border-0 py-3">
              <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 rounded-3 p-2 me-3">
                  <i class="bi bi-check-circle text-success"></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-1">Deployment completed</h6>
                  <small class="text-muted">15 monitors deployed to Marketing Dept</small>
                </div>
                <small class="text-muted">3 hours ago</small>
              </div>
            </a>
            <a href="#" class="list-group-item list-group-item-action border-0 py-3">
              <div class="d-flex align-items-center">
                <div class="bg-info bg-opacity-10 rounded-3 p-2 me-3">
                  <i class="bi bi-person-plus text-info"></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-1">New supplier added</h6>
                  <small class="text-muted">Tech Solutions Inc. added as supplier</small>
                </div>
                <small class="text-muted">Yesterday</small>
              </div>
            </a>
            <a href="#" class="list-group-item list-group-item-action border-0 py-3">
              <div class="d-flex align-items-center">
                <div class="bg-danger bg-opacity-10 rounded-3 p-2 me-3">
                  <i class="bi bi-x-circle text-danger"></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-1">Item removed</h6>
                  <small class="text-muted">Obsolete hardware removed from inventory</small>
                </div>
                <small class="text-muted">2 days ago</small>
              </div>
            </a>
          </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
          <a href="#" class="btn btn-outline-primary w-100 rounded-3">
            <i class="bi bi-clock-history me-2"></i>View All Activity
          </a>
        </div>
      </div>
    </div>
  </div>

</div>

<style>
  /* Modern Dashboard Styles */
  .text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .card-hover {
    transition: all 0.3s ease;
    border-radius: 12px;
  }

  .card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
  }

  .action-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    background: #f8f9fa;
  }

  .action-card:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  }

  .list-group-item {
    transition: all 0.2s ease;
  }

  .list-group-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
  }

  .rounded-3 {
    border-radius: 12px !important;
  }

  .rounded-2 {
    border-radius: 8px !important;
  }

  /* Custom badge styling */
  .badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
  }

  /* Smooth transitions */
  * {
    transition: background-color 0.2s ease;
  }

  /* Card header styling */
  .card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .d-flex.justify-content-between {
      flex-direction: column;
      gap: 1rem;
    }
    
    .d-flex.gap-2 {
      width: 100%;
      justify-content: flex-start;
    }
  }
</style>

@endsection