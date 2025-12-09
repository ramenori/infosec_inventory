@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
  <h1 class="h3 my-3">SUPPLIERS</h1>

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bi bi-house"></i></a></li>
      <li class="breadcrumb-item active" aria-current="page">Suppliers</li>
    </ol>
  </nav>

  {{-- Success Message --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- Top Buttons --}}
  <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
    <button type="button" class="btn btn-dark">Filter by</button>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
      Add Supplier
    </button>

    <div class="btn-group">
      <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        Export
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#">Export as Excel</a></li>
        <li><a class="dropdown-item" href="#">Export as PDF</a></li>
      </ul>
    </div>
  </div>

  {{-- Search Bar --}}
  <div class="mb-3 d-flex justify-content-end">
    <form method="GET" action="{{ route('admin.suppliers') }}" class="d-flex">
      <input
        class="form-control me-2" 
        type="search" 
        name="search" 
        placeholder="Search suppliers" 
        aria-label="Search"
        value="{{ request('search') }}"
        style="max-width: 250px;"
      />
      <button class="btn btn-outline-secondary" type="submit" title="Search">
        <i class="bi bi-search"></i>
      </button>
    </form>
  </div>

  {{-- Suppliers Table --}}
  <div class="table-responsive">
    <table class="table table-bordered text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>SUPPLIER</th>
          <th>LOCATION</th>
          <th>CONTACT</th>
          <th>ACTIONS</th>
        </tr>
      </thead>
      <tbody>
        @forelse($suppliers as $supplier)
            <tr>
                <td>{{ $supplier->name }}</td>
                <td>{{ $supplier->location }}</td>
                <td>{{ $supplier->contact }}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" title="View" data-bs-toggle="modal" data-bs-target="#editSupplierModal{{ $supplier->id }}">
                        <i class="bi bi-eye"></i>
                    </button>
                    <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this supplier?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>

            {{-- Edit Supplier Modal --}}
            <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1" aria-labelledby="editSupplierModalLabel{{ $supplier->id }}" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="editSupplierModalLabel{{ $supplier->id }}">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="name{{ $supplier->id }}" class="form-label">Supplier Name</label>
                        <input type="text" class="form-control" id="name{{ $supplier->id }}" name="name" value="{{ $supplier->name }}" required>
                      </div>
                      <div class="mb-3">
                        <label for="location{{ $supplier->id }}" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location{{ $supplier->id }}" name="location" value="{{ $supplier->location }}">
                      </div>
                      <div class="mb-3">
                        <label for="contact{{ $supplier->id }}" class="form-label">Contact</label>
                        <input type="text" class="form-control" id="contact{{ $supplier->id }}" name="contact" value="{{ $supplier->contact }}">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Update Supplier</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        @empty
            <tr>
                <td colspan="4" class="text-center">No suppliers found.</td>
            </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination and entries info --}}
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div>Showing {{ $suppliers->firstItem() }} to {{ $suppliers->lastItem() }} of {{ $suppliers->total() }} entries</div>
    {{ $suppliers->links() }}
  </div>
</div>

{{-- Add Supplier Modal --}}
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSupplierModalLabel">Add New Supplier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.suppliers.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">Supplier Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control" id="location" name="location">
          </div>
          <div class="mb-3">
            <label for="contact" class="form-label">Contact</label>
            <input type="text" class="form-control" id="contact" name="contact">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Add Supplier</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection