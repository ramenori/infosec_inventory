@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
            <h1 class="h2 mb-1 fw-bold text-gradient">Account Management</h1>
            <p class="text-muted mb-0">Manage user accounts and permissions</p>
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
                <i class="bi bi-people"></i> Accounts
            </li>
        </ol>
    </nav>

    <div class="row">
        {{-- Add User Panel (Left) --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-plus-circle me-2"></i> Add New Account
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

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            <strong>Validation Error:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.accounts.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label small fw-semibold">
                                Username
                            </label>
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   placeholder="Enter username" 
                                   required>
                            @error('username')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label small fw-semibold">
                                Full Name
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Enter full name" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-semibold">
                                Email Address
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="Enter email address" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label small fw-semibold">
                                Password
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter password (minimum 6 characters)" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Create Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Users List Panel (Right) --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-table me-2"></i> User Accounts
                        </h6>
                        <div class="badge bg-primary">{{ count($users) }} Users</div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light border-bottom">
                                    <tr>
                                        <th class="px-4 py-3 small fw-semibold">Username</th>
                                        <th class="px-4 py-3 small fw-semibold">Full Name</th>
                                        <th class="px-4 py-3 small fw-semibold">Email</th>
                                        <th class="px-4 py-3 small fw-semibold text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr class="align-middle">
                                            <td class="px-4 py-3">
                                                <i class="bi bi-person-circle me-2"></i>
                                                <strong>{{ $user->username }}</strong>
                                            </td>
                                            <td class="px-4 py-3">{{ $user->name }}</td>
                                            <td class="px-4 py-3 text-muted small">{{ $user->email }}</td>
                                            <td class="px-4 py-3 text-end">
                                                <a href="{{ route('admin.accounts.edit', $user->id) }}" 
                                                   class="btn btn-sm btn-outline-warning">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal{{ $user->id }}"
                                                        @if(count($users) == 1) disabled title="Cannot delete the only account" @endif>
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>

                                                <!-- Delete Confirmation Modal -->
                                                <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Confirm Delete</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to delete the account for <strong>{{ $user->name }}</strong>? This action cannot be undone.
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form action="{{ route('admin.accounts.destroy', $user->id) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No user accounts found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
