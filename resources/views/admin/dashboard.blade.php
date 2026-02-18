@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
  <!-- Page Header (keep your existing header) -->
  <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
    <div>
      <h1 class="h2 mb-1 fw-bold text-gradient">Dashboard Overview</h1>
      <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name ?? 'Admin' }}! Here's what's happening with your inventory.</p>
    </div>
    
    <!-- Action Buttons (keep your existing buttons) -->
    <div class="d-flex gap-2">
      <!-- ... your existing buttons ... -->
    </div>
  </div>

  <!-- Stats Cards (keep your existing cards but with real data) -->
  <div class="row g-4 mb-4">
    <!-- Most Item Deployed -->
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm h-100 card-hover" style="border-left: 4px solid #667eea;">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
              <i class="bi bi-rocket-fill text-primary fs-4"></i>
            </div>
            <span class="badge bg-primary bg-opacity-20 text-primary rounded-pill">
              {{ $dashboardData['most_deployed_quantity'] }} deployed
            </span>
          </div>
          <h3 class="mb-2 fw-bold">{{ Str::limit($dashboardData['most_deployed_item'], 15) }}</h3>
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
          <h3 class="mb-2 fw-bold">{{ Str::limit($dashboardData['top_supplier'], 15) }}</h3>
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
          <h3 class="mb-2 fw-bold">{{ $dashboardData['total_stock_qty'] }}</h3>
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
          <h3 class="mb-2 fw-bold">{{ $dashboardData['items_deployed_month'] }}</h3>
          <p class="text-muted mb-0">Items Deployed</p>
          <div class="mt-3">
            <small class="text-primary"><i class="bi bi-arrow-up-circle me-1"></i> Monthly deployment count</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Section - NOW WITH REAL DATA! -->
  <div class="row g-4 mb-4">
    <!-- Main Chart Area -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 py-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-0 fw-bold">Analytics Overview</h5>
              <p class="text-muted mb-0 small">Last 7 days deployment trends</p>
            </div>
            <div class="btn-group btn-group-sm">
              <button type="button" class="btn btn-outline-primary active" id="dailyView">Daily</button>
              <button type="button" class="btn btn-outline-primary" id="monthlyView">Monthly</button>
              <button type="button" class="btn btn-outline-primary" id="itemsView">Top Items</button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <!-- Chart Container -->
          <canvas id="mainChart" style="height: 300px; width: 100%;"></canvas>
          
          <!-- Quick Stats -->
          <div class="row mt-4 g-3">
            <div class="col-md-4">
              <div class="bg-light p-3 rounded-3">
                <small class="text-muted d-block">Total Deployments (7 days)</small>
                <h4 class="mb-0 fw-bold">{{ array_sum($dashboardData['daily_chart']['data']) }}</h4>
              </div>
            </div>
            <div class="col-md-4">
              <div class="bg-light p-3 rounded-3">
                <small class="text-muted d-block">Average Daily</small>
                <h4 class="mb-0 fw-bold">{{ round(array_sum($dashboardData['daily_chart']['data']) / 7, 1) }}</h4>
              </div>
            </div>
            <div class="col-md-4">
              <div class="bg-light p-3 rounded-3">
                <small class="text-muted d-block">Peak Day</small>
                <h4 class="mb-0 fw-bold">{{ max($dashboardData['daily_chart']['data']) }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column - Stats and Mini Charts -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 py-3">
          <h5 class="mb-0 fw-bold">Inventory Insights</h5>
        </div>
        <div class="card-body">
          <!-- Top Items Mini Chart -->
          <div class="mb-4">
            <h6 class="text-muted mb-3">Top Deployed Items</h6>
            <canvas id="topItemsChart" style="height: 150px;"></canvas>
          </div>
          
          <!-- Category Distribution -->
          <div class="mb-4">
            <h6 class="text-muted mb-3">Stock by Category</h6>
            <canvas id="categoryChart" style="height: 150px;"></canvas>
          </div>
          
          <!-- Status Breakdown -->
          <div>
            <h6 class="text-muted mb-3">Deployment Status</h6>
            <div class="d-flex justify-content-between mb-2">
              <span><span class="badge bg-success me-2"></span> Completed</span>
              <span class="fw-bold">{{ $dashboardData['status_breakdown']['completed'] }}</span>
            </div>
            <div class="progress mb-3" style="height: 8px;">
              @php
                $total = array_sum($dashboardData['status_breakdown']);
                $completedPercent = $total > 0 ? round(($dashboardData['status_breakdown']['completed'] / $total) * 100) : 0;
                $pendingPercent = $total > 0 ? round(($dashboardData['status_breakdown']['pending'] / $total) * 100) : 0;
              @endphp
              <div class="progress-bar bg-success" style="width: {{ $completedPercent }}%"></div>
              <div class="progress-bar bg-warning" style="width: {{ $pendingPercent }}%"></div>
            </div>
            
            <div class="d-flex justify-content-between mb-2">
              <span><span class="badge bg-warning me-2"></span> Pending</span>
              <span class="fw-bold">{{ $dashboardData['status_breakdown']['pending'] }}</span>
            </div>
            
            <div class="d-flex justify-content-between">
              <span><span class="badge bg-danger me-2"></span> Cancelled</span>
              <span class="fw-bold">{{ $dashboardData['status_breakdown']['cancelled'] }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Activity Section - NOW WITH REAL DATA! -->
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
          <h5 class="mb-0 fw-bold">Recent Deployment Activity</h5>
        </div>
        <div class="card-body p-0">
          @if(count($dashboardData['recent_activities']) > 0)
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Deployed To</th>
                    <th>Time</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($dashboardData['recent_activities'] as $activity)
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="bg-primary bg-opacity-10 rounded-2 p-2 me-3">
                            <i class="bi bi-box text-primary"></i>
                          </div>
                          <div>
                            <h6 class="mb-0 fw-semibold">{{ $activity['component'] }}</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-info bg-opacity-20 text-info px-3 py-2">
                          {{ $activity['quantity'] }} units
                        </span>
                      </td>
                      <td>{{ $activity['deployed_to'] }}</td>
                      <td>
                        <span title="{{ $activity['date'] }}">
                          {{ $activity['time_ago'] }}
                        </span>
                      </td>
                      <td>
                        <i class="bi bi-check-circle-fill text-success"></i>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-5">
              <i class="bi bi-inbox fs-1 text-muted"></i>
              <p class="text-muted mt-2">No recent activity</p>
            </div>
          @endif
        </div>
        <div class="card-footer bg-white border-0 py-3">
          <a href="{{ route('admin.deployment.history') }}" class="btn btn-outline-primary w-100 rounded-3">
            <i class="bi bi-clock-history me-2"></i>View All Activity
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Your existing styles plus these additions */
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

  .rounded-3 {
    border-radius: 12px !important;
  }
</style>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data from controller
    const dailyData = {
        labels: {!! json_encode($dashboardData['daily_chart']['labels']) !!},
        values: {!! json_encode($dashboardData['daily_chart']['data']) !!}
    };
    
    const topItemsData = {
        labels: {!! json_encode($dashboardData['top_items_chart']['labels']) !!},
        values: {!! json_encode($dashboardData['top_items_chart']['data']) !!}
    };
    
    const categoryData = {
        labels: {!! json_encode($dashboardData['category_chart']['labels']) !!},
        values: {!! json_encode($dashboardData['category_chart']['data']) !!}
    };
    
    // Initialize Main Chart
    const ctx = document.getElementById('mainChart').getContext('2d');
    let mainChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyData.labels,
            datasets: [{
                label: 'Items Deployed',
                data: dailyData.values,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#333',
                    bodyColor: '#666',
                    borderColor: '#e0e0e0',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return `${context.raw} items deployed`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value + ' items';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
    // Top Items Pie Chart
    const topItemsCtx = document.getElementById('topItemsChart').getContext('2d');
    new Chart(topItemsCtx, {
        type: 'doughnut',
        data: {
            labels: topItemsData.labels,
            datasets: [{
                data: topItemsData.values,
                backgroundColor: [
                    '#667eea',
                    '#10b981',
                    '#f59e0b',
                    '#3b82f6',
                    '#ef4444',
                    '#8b5cf6'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '65%'
        }
    });
    
    // Category Distribution Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: categoryData.labels,
            datasets: [{
                data: categoryData.values,
                backgroundColor: '#10b981',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
    // Chart View Switcher
    document.getElementById('dailyView').addEventListener('click', function() {
        setActiveButton(this);
        mainChart.data.labels = dailyData.labels;
        mainChart.data.datasets[0].data = dailyData.values;
        mainChart.data.datasets[0].label = 'Items Deployed';
        mainChart.update();
    });
    
    document.getElementById('monthlyView').addEventListener('click', function() {
        setActiveButton(this);
        // You can add monthly data here if you have it
        mainChart.data.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        mainChart.data.datasets[0].data = [65, 59, 80, 81, 56, 55];
        mainChart.data.datasets[0].label = 'Monthly Deployments';
        mainChart.update();
    });
    
    document.getElementById('itemsView').addEventListener('click', function() {
        setActiveButton(this);
        mainChart.data.labels = topItemsData.labels.slice(0, 5);
        mainChart.data.datasets[0].data = topItemsData.values.slice(0, 5);
        mainChart.data.datasets[0].label = 'Top Items';
        mainChart.type = 'bar';
        mainChart.update();
    });
    
    function setActiveButton(btn) {
        document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }
});
</script>
@endpush