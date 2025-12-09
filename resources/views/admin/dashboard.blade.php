@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
  <h1 class="h3 my-3">DASHBOARD</h1>

  {{-- Four Cards in One Row --}}
  <div class="mb-md-2">
    <button type="button" class="btn btn-dark">Filter by</button>
    <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        Export
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#">Export as Excel</a></li>
        <li><a class="dropdown-item" href="#">Export as PDF</a></li>
      </ul>
  </div>
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card h-100 border-primary">
        <div class="card-header bg-primary text-white">
          Most Item Deployed
        </div>
        <div class="card-body d-flex flex-column justify-content-center align-items-center">
          <h2 class="display-5">-</h2>
          <p class="text-muted">Top deployed inventory item will appear here.</p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card h-100 border-success">
        <div class="card-header bg-success text-white">
          Top Supplier
        </div>
        <div class="card-body d-flex flex-column justify-content-center align-items-center">
          <h2 class="display-5">-</h2>
          <p class="text-muted">Top supplier information will appear here.</p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card h-100 border-warning">
        <div class="card-header bg-warning text-white">
          Total Stock QTY
        </div>
        <div class="card-body d-flex flex-column justify-content-center align-items-center">
          <h2 class="display-5">-</h2>
          <p class="text-muted">Total stock quantity in the inventory.</p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card h-100 border-info">
        <div class="card-header bg-info text-white">
          No. of Items Deployed This Month
        </div>
        <div class="card-body d-flex flex-column justify-content-center align-items-center">
          <h2 class="display-5">-</h2>
          <p class="text-muted">Number of deployed items this month.</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Analytics Section Full Width Below --}}
  <div class="card border-secondary">
    <div class="card-header bg-secondary text-white">
      Analytics
    </div>
    <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 300px;">
      <p class="text-muted fs-5">Analytics visualizations and charts will appear here.</p>
    </div>
  </div>
</div>
@endsection