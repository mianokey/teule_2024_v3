@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">
            Store Item Categories
        </h6>

        <a href="{{ route('admin.store-categories.create') }}"
           class="btn btn-primary">
            <i class="fa fa-plus"></i>
            Add Category
        </a>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($categories->count())

            <div class="table-responsive">

                <table class="table table-striped table-bordered">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($categories as $category)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <strong>{{ $category->name }}</strong>
                                </td>

                                <td>
                                    {{ $category->description ?: '—' }}
                                </td>

                                <td>
                                    <span class="badge badge-info">
                                        {{ $category->items_count }}
                                    </span>
                                </td>

                                <td>

                                    @if($category->is_active)
                                        <span class="badge badge-success">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            Inactive
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    <a href="{{ route('admin.store-categories.show', $category) }}"
                                       class="btn btn-sm btn-info">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.store-categories.edit', $category) }}"
                                       class="btn btn-sm btn-warning">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.store-categories.toggle-status', $category) }}"
                                          method="POST"
                                          class="d-inline">

                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="btn btn-sm {{ $category->is_active ? 'btn-secondary' : 'btn-success' }}"
                                                onclick="return confirm('{{ $category->is_active ? 'Deactivate this category?' : 'Activate this category?' }}')">

                                            <i class="fa {{ $category->is_active ? 'fa-ban' : 'fa-check' }}"></i>

                                        </button>

                                    </form>

                                    @if($category->items_count == 0)

                                        <form action="{{ route('admin.store-categories.destroy', $category) }}"
                                              method="POST"
                                              class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this category?')">

                                                <i class="fa fa-trash"></i>

                                            </button>

                                        </form>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="text-center py-5">

                <i class="fa fa-folder-open fa-3x text-muted mb-3"></i>

                <h5>No Store Item Categories</h5>

                <p class="text-muted">
                    Create your first store item category.
                </p>

                <a href="{{ route('admin.store-categories.create') }}"
                   class="btn btn-primary">

                    <i class="fa fa-plus"></i>
                    Add Category

                </a>

            </div>

        @endif

    </div>

</div>

@endsection