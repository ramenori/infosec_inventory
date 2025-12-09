@extends('layouts.admin')

@section('content')
<div class="container" style="max-width: 700px; margin-top: 2rem;">

  <h2 class="mb-4">Add New Category</h2>

  {{-- Success Message --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('admin.category.store') }}" method="POST" class="mb-5">
    @csrf
    <div class="input-group">
      <span class="input-group-text">
        <i class="bi bi-bookmark"></i>
      </span>
      <input type="text" class="form-control" name="name" placeholder="Enter category name" aria-label="Category name" required />
      <button type="submit" class="btn btn-primary">+ Add Category</button>
    </div>
  </form>

  <div class="card">
    <div class="card-header bg-dark text-white d-flex align-items-center gap-2">
      <i class="bi bi-bookmark"></i>
      <span>CATEGORY LIST</span>
    </div>
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>Name</th>
            <th style="width: 100px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($categories as $category)
            <tr>
              <td>{{ $category->name }}</td>
              <td class="text-center col-md-3">
                <a href="{{ route('admin.category.edit', $category->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('admin.category.destroy', $category->id) }}" method="POST" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="2" class="text-center">No categories found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection