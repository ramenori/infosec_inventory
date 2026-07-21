@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
  {{-- Header Section --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1 fw-bold text-gradient">Inventory Activity Log</h1>
      <p class="text-muted mb-0">Track all actions performed across your inventory items</p>
    </div>
    <a href="{{ route('admin.inventory') }}" class="btn btn-outline-secondary btn-sm px-3">
      <i class="bi bi-arrow-left me-1"></i> Back to Inventory
    </a>
  </div>

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded-3 mb-0">
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

  {{-- Active User Info Bar --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
      <div class="row align-items-center">
        <div class="col-md-6 border-end-md">
          <div class="d-flex align-items-center">
            <div class="avatar-box bg-primary bg-opacity-10 text-primary rounded-3 me-3 d-flex align-items-center justify-content-center">
              <i class="bi bi-person-fill fs-4"></i>
            </div>
            <div>
              <small class="text-muted d-block">Current Administrator</small>
              <h6 class="mb-0 fw-bold text-dark">{{ auth()->user()->name }}</h6>
            </div>
          </div>
        </div>
        <div class="col-md-6 ps-md-4 mt-3 mt-md-0">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <small class="text-muted d-block">Total Operations Recorded</small>
              <h6 class="mb-0 fw-bold text-primary fs-5">{{ $logs->total() }} {{ $logs->total() === 1 ? 'Action' : 'Actions' }}</h6>
            </div>
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
              <i class="bi bi-shield-check text-success me-1"></i> Audit Active
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Logs Table Card --}}
  <div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-light py-3 px-4 border-bottom">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold text-dark">
          <i class="bi bi-journal-text me-2 text-primary"></i> Activity History
        </h6>
        <small class="text-muted">
          Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
        </small>
      </div>
    </div>

    <div class="card-body p-0">
      @if($logs->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-4 border-0 text-secondary fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">DATE & TIME</th>
                <th class="border-0 text-secondary fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">ACTION</th>
                <th class="border-0 text-secondary fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">PERFORMED BY</th>
                <th class="border-0 text-secondary fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">AFFECTED ITEM</th>
                <th class="pe-4 border-0 text-secondary fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">LOG DETAILS</th>
              </tr>
            </thead>
            <tbody>
              @php
                $actionConfig = [
                  'created'  => ['bg' => 'bg-success', 'icon' => 'bi-plus-circle', 'label' => 'Created'],
                  'updated'  => ['bg' => 'bg-warning text-dark', 'icon' => 'bi-pencil-square', 'label' => 'Updated'],
                  'deleted'  => ['bg' => 'bg-danger', 'icon' => 'bi-trash', 'label' => 'Deleted'],
                  'viewed'   => ['bg' => 'bg-info text-dark', 'icon' => 'bi-eye', 'label' => 'Viewed'],
                  'exported' => ['bg' => 'bg-primary', 'icon' => 'bi-download', 'label' => 'Exported'],
                ];
              @endphp

              @foreach($logs as $log)
                @php
                  $cfg = $actionConfig[$log->action] ?? ['bg' => 'bg-secondary', 'icon' => 'bi-box', 'label' => ucfirst($log->action)];
                @endphp
                <tr>
                  {{-- Date & Time --}}
                  <td class="ps-4 py-3">
                    <div class="d-flex flex-column">
                      <span class="fw-semibold text-dark small">{{ optional($log->created_at)->format('M d, Y') ?? 'N/A' }}</span>
                      <small class="text-muted" style="font-size: 0.75rem;">{{ optional($log->created_at)->format('h:i:s A') }}</small>
                    </div>
                  </td>

                  {{-- Action Badge --}}
                  <td>
                    <span class="badge {{ $cfg['bg'] }} px-3 py-2 fw-medium d-inline-flex align-items-center gap-2" style="font-size: 0.8rem; border-radius: 6px;">
                      <i class="bi {{ $cfg['icon'] }}"></i> {{ $cfg['label'] }}
                    </span>
                  </td>

                  {{-- User --}}
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar-circle me-2 bg-primary text-white fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px;">
                        {{ strtoupper(substr($log->user?->name ?? 'U', 0, 1)) }}
                      </div>
                      <div class="d-flex flex-column">
                        <span class="fw-semibold text-dark small">{{ $log->user?->name ?? 'Unknown User' }}</span>
                        <small class="text-muted" style="font-size: 0.75rem;">{{ $log->user?->email ?? '-' }}</small>
                      </div>
                    </div>
                  </td>

                  {{-- Component Item --}}
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="item-icon-bg me-2 rounded bg-light text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-box"></i>
                      </div>
                      <span class="fw-semibold text-dark small">{{ $log->component ?? 'N/A' }}</span>
                    </div>
                  </td>

                  {{-- Log Details --}}
                  <td class="pe-4">
                    @if(str_contains($log->details, 'Added') || str_contains($log->details, 'Deducted'))
                      <span class="fw-medium text-dark small">{{ $log->details }}</span>
                    @else
                      <span class="text-muted small">{{ $log->details ?? '-' }}</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
          <div class="card-footer bg-light py-3 border-top">
            <div class="d-flex justify-content-center">
              {{ $logs->links() }}
            </div>
          </div>
        @endif
      @else
        <div class="p-5 text-center">
          <i class="bi bi-inbox display-4 text-muted mb-3 d-block"></i>
          <h5 class="text-muted fw-semibold">No activity logs found</h5>
          <p class="text-muted small mb-0">No actions have been recorded in the inventory activity logs yet.</p>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Custom CSS --}}
<style>
.text-gradient {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.avatar-box {
  width: 44px;
  height: 44px;
}

.table tbody tr {
  transition: background-color 0.15s ease;
}

.table tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.015);
}

@media (min-width: 768px) {
  .border-end-md {
    border-right: 1px solid #dee2e6 !important;
  }
}
</style>
@endsection