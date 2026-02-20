@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
            <h1 class="h2 mb-1 fw-bold text-gradient">Categories</h1>
            <p class="text-muted mb-0">Manage product categories and classifications</p>
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
                <i class="bi bi-tags"></i> Categories
            </li>
        </ol>
    </nav>

    <div class="row">
        {{-- Add Category Panel (Left) --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-plus-circle me-2"></i> Add New Category
                    </h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.category.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="categoryName" class="form-label small fw-semibold">
                                Category Name
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="categoryName" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Enter category name" 
                                   required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Add Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Categories List Panel (Right) --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-table me-2"></i> Category List
                        </h6>
                        <div class="d-flex align-items-center gap-2">
                            <form method="GET" action="{{ route('admin.category') }}" class="d-flex">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="search" 
                                           class="form-control border-start-0" 
                                           name="search" 
                                           placeholder="Search..." 
                                           value="{{ request('search') }}">
                                </div>
                                @if(request('search'))
                                    <a href="{{ route('admin.category') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                        <i class="bi bi-x"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-4">CATEGORY</th>
                                    <th class="border-0 text-end pe-4">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr class="hover-shadow">
                                        <td class="ps-4 align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="category-icon me-3">
                                                    <i class="bi bi-folder text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">{{ $category->name }}</h6>
                                                    <small class="text-muted">
                                                        Created {{ $category->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4 align-middle">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.category.edit', $category->id) }}" 
                                                   class="btn btn-sm btn-outline-primary border" 
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.category.destroy', $category->id) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger border"
                                                            title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this category?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="bi bi-tags display-4 text-muted mb-3"></i>
                                                <h5 class="text-muted">No categories found</h5>
                                                @if(request('search'))
                                                    <p class="text-muted mb-4">No results for "{{ request('search') }}"</p>
                                                    <a href="{{ route('admin.category.index') }}" class="btn btn-outline-secondary">
                                                        Clear Search
                                                    </a>
                                                @else
                                                    <p class="text-muted mb-4">Start by adding a category on the left</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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

.category-icon {
    width: 36px;
    height: 36px;
    background: rgba(13, 110, 253, 0.1);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hover-shadow:hover {
    background-color: rgba(0, 0, 0, 0.02);
    transition: all 0.2s ease;
}

.empty-state {
    padding: 2rem 1rem;
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

.input-group-sm .form-control,
.input-group-sm .input-group-text {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
}
</style>
@endsection