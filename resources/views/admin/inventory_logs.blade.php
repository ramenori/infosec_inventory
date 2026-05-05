@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
  {{-- Header Section --}}
  <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
    <div>
      <h1 class="h2 mb-1 fw-bold text-gradient">Inventory Activity Log</h1>
      <p class="text-muted mb-0">View all actions performed on inventory items</p>
    </div>
    <a href="{{ route('admin.inventory') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to Inventory
    </a>
  </div>

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
      <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
          <i class="bi bi-house-door"></i> Dashboard
        </a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{ route('admin.inventory') }}" class="text-decoration-none">
          <i class="bi bi-boxes"></i> Inventory
        </a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        <i class="bi bi-clock-history"></i> Activity Logs
      </li>
    </ol>
  </nav>

  {{-- User Info Card --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="d-flex align-items-center">
            <div class="display-6 me-3">
              <i class="bi bi-person-circle text-primary"></i>
            </div>
            <div>
              <small class="text-muted d-block">Current User</small>
              <h6 class="mb-0 fw-bold">{{ auth()->user()->name }}</h6>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <small class="text-muted d-block mb-2">Total Actions Recorded</small>
          <h6 class="mb-0 fw-bold text-info">{{ $logs->total() }} {{ $logs->total() === 1 ? 'action' : 'actions' }}</h6>
        </div>
      </div>
    </div>
  </div>

  {{-- Logs Table --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-light py-3">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">
          <i class="bi bi-table me-2"></i> Activity History
        </h6>
        <small class="text-muted">
          Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
        </small>
      </div>
    </div>

    <div class="card-body p-0">
      @if($logs->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="border-0">DATE & TIME</th>
                <th class="border-0">ACTION</th>
                <th class="border-0">USER</th>
                <th class="border-0">ITEM</th>
                <th class="border-0">DETAILS</th>
              </tr>
            </thead>
            <tbody>
              @foreach($logs as $log)
                <tr>
                  <td>
                    <div class="d-flex flex-column">
                      <span class="fw-semibold small">{{ $log->created_at->format('M d, Y') }}</span>
                      <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                    </div>
                  </td>
                  <td>
                    <span class="badge {{ getActionBadgeClass($log->action) }} px-3 py-2">
                      {!! getActionIcon($log->action) !!} {{ formatAction($log->action) }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="user-avatar me-2" style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                        {{ substr($log->user?->name ?? 'U', 0, 1) }}
                      </div>
                      <div class="d-flex flex-column">
                        <span class="fw-semibold small">{{ $log->user?->name ?? 'Unknown User' }}</span>
                        <small class="text-muted">{{ $log->user?->email ?? '-' }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="item-icon me-2">
                        <i class="bi bi-box text-primary"></i>
                      </div>
                      <span class="fw-semibold">{{ $log->component ?? 'N/A' }}</span>
                    </div>
                  </td>
                  <td>
                    <small class="text-muted">{{ $log->details ?? '-' }}</small>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
          <div class="card-footer bg-light py-3">
            <div class="d-flex justify-content-center">
              {{ $logs->links() }}
            </div>
          </div>
        @endif
      @else
        <div class="p-5 text-center">
          <i class="bi bi-inbox display-4 text-muted mb-3"></i>
          <h5 class="text-muted">No activity logs found</h5>
          <p class="text-muted mb-0">You have not performed any actions on inventory items in the last 30 days.</p>
        </div>
      @endif
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

.table tbody tr {
  transition: all 0.2s ease;
}

.table tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.02);
}
</style>

{{-- PHP Helpers --}}
@php
function getActionBadgeClass($action) {
  $actionMap = [
    'created' => 'bg-success',
    'updated' => 'bg-warning',
    'deleted' => 'bg-danger',
    'viewed' => 'bg-info',
    'exported' => 'bg-primary'
  ];
  return $actionMap[$action] ?? 'bg-secondary';
}

function getActionIcon($action) {
  $iconMap = [
    'created' => 'bi-plus-circle',
    'updated' => 'bi-pencil-square',
    'deleted' => 'bi-trash',
    'viewed' => 'bi-eye',
    'exported' => 'bi-download'
  ];
  $icon = $iconMap[$action] ?? 'bi-box';
  return '<i class="bi ' . $icon . ' me-1"></i>';
}

function formatAction($action) {
  $actionMap = [
    'created' => 'Created',
    'updated' => 'Updated',
    'deleted' => 'Deleted',
    'viewed' => 'Viewed',
    'exported' => 'Exported'
  ];
  return $actionMap[$action] ?? ucfirst($action);
}
@endphp
@endsection
