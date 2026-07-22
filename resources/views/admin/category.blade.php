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

    {{-- Main Workspace Row --}}
    <div class="row g-4">
        {{-- Add Category Panel (Left) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-plus-circle me-2 text-primary"></i> Add New Category
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 border-0 shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.category.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="categoryName" class="form-label small fw-semibold text-secondary">
                                Category Name <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-folder-plus text-muted"></i>
                                </span>
                                <input type="text" 
                                       class="form-control border-start-0 ps-0" 
                                       id="categoryName" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="e.g. CCTV, Access Control..." 
                                       required>
                            </div>
                            <small class="text-muted mt-1 d-block" style="font-size: 11px;">Categorizing helps organize inventory and deployment reporting.</small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-semibold">
                                <i class="bi bi-plus-lg me-1"></i> Save Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Categories List Panel (Right) --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="bi bi-grid-3x3-gap me-2 text-primary"></i> Category List
                        </h6>
                        <div>
                            <form method="GET" action="{{ route('admin.category') }}" class="d-flex">
                                <div class="input-group input-group-sm" style="width: 220px;">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="search" 
                                           class="form-control border-start-0 ps-0" 
                                           name="search" 
                                           placeholder="Search category..." 
                                           value="{{ request('search') }}">
                                </div>
                                @if(request('search'))
                                    <a href="{{ route('admin.category') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                        <i class="bi bi-x-lg"></i>
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
                                    <th class="border-0 ps-4 text-secondary fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">CATEGORY</th>
                                    <th class="border-0 text-center text-secondary fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">ITEMS ASSIGNED</th>
                                    <th class="border-0 text-end pe-4 text-secondary fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    @php
                                        $iconMap = [
                                            'Access Control' => ['icon' => 'bi-shield-lock-fill', 'bg' => 'rgba(13, 110, 253, 0.1)', 'color' => '#0d6efd'],
                                            'CCTV'           => ['icon' => 'bi-camera-video-fill', 'bg' => 'rgba(13, 202, 240, 0.1)', 'color' => '#0dcaf0'],
                                            'GPS'            => ['icon' => 'bi-geo-alt-fill', 'bg' => 'rgba(220, 53, 69, 0.1)', 'color' => '#dc3545'],
                                            'Wireless Alarm' => ['icon' => 'bi-bell-fill', 'bg' => 'rgba(255, 193, 7, 0.15)', 'color' => '#b58100'],
                                            'Network'        => ['icon' => 'bi-wifi', 'bg' => 'rgba(25, 135, 84, 0.1)', 'color' => '#198754'],
                                            'Consumables'    => ['icon' => 'bi-box-seam-fill', 'bg' => 'rgba(108, 117, 125, 0.1)', 'color' => '#6c757d'],
                                        ];
                                        $style = $iconMap[$category->name] ?? ['icon' => 'bi-folder-fill', 'bg' => 'rgba(13, 110, 253, 0.1)', 'color' => '#0d6efd'];
                                        $itemCount = \App\Models\Inventory::where('category', $category->name)->count();
                                    @endphp
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="category-icon me-3" style="background-color: {{ $style['bg'] }}; color: {{ $style['color'] }};">
                                                    <i class="bi {{ $style['icon'] }} fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold text-dark">{{ $category->name }}</h6>
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        Created {{ optional($category->created_at)->diffForHumans() ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill fw-semibold">
                                                <i class="bi bi-box me-1 text-primary"></i> {{ $itemCount }} {{ $itemCount === 1 ? 'item' : 'items' }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.category.edit', $category->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit Category">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.category.destroy', $category->id) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Delete Category"
                                                            onclick="return confirm('Are you sure you want to delete this category?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="bi bi-tags display-5 text-muted mb-3 d-block"></i>
                                                <h6 class="text-muted fw-bold">No Categories Found</h6>
                                                @if(request('search'))
                                                    <p class="text-muted small mb-3">No category matching "{{ request('search') }}"</p>
                                                    <a href="{{ route('admin.category') }}" class="btn btn-sm btn-outline-secondary">
                                                        Clear Search
                                                    </a>
                                                @else
                                                    <p class="text-muted small mb-0">Add your first category using the panel on the left.</p>
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
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.category-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table tbody tr {
    transition: background-color 0.15s ease;
}

.table tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.015);
}
</style>
@endsection