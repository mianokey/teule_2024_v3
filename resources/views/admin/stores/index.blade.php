@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h6 class="card-title mb-0">
            Stores
        </h6>

        <a href="{{ route('admin.stores.create') }}"
           class="btn btn-primary">

            <i class="fa fa-plus"></i>
            Add Store

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

        @if($stores->count())

            <div class="table-responsive">

                <table class="table table-striped table-bordered">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Store</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Stock Records</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($stores as $store)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <strong>{{ $store->name }}</strong>
                                </td>

                                <td>
                                    <span class="badge badge-info">
                                        {{ $store->code }}
                                    </span>
                                </td>

                                <td>
                                    {{ $store->description ?: '—' }}
                                </td>

                                <td>
                                    {{ $store->stocks_count }}
                                </td>

                                <td>

                                    @if($store->is_active)

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

                                    <a href="{{ route('admin.stores.show', $store) }}"
                                       class="btn btn-sm btn-info">

                                        <i class="fa fa-eye"></i>

                                    </a>

                                    <a href="{{ route('admin.stores.edit', $store) }}"
                                       class="btn btn-sm btn-warning">

                                        <i class="fa fa-edit"></i>

                                    </a>

                                    <form action="{{ route('admin.stores.toggle-status', $store) }}"
                                          method="POST"
                                          class="d-inline">

                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="btn btn-sm {{ $store->is_active ? 'btn-secondary' : 'btn-success' }}"
                                                onclick="return confirm('{{ $store->is_active ? 'Deactivate this store?' : 'Activate this store?' }}')">

                                            <i class="fa {{ $store->is_active ? 'fa-ban' : 'fa-check' }}"></i>

                                        </button>

                                    </form>

                                    @if($store->stocks_count == 0)

                                        <form action="{{ route('admin.stores.destroy', $store) }}"
                                              method="POST"
                                              class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this store?')">

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

                <i class="fa fa-archive fa-3x text-muted mb-3"></i>

                <h5>No Stores</h5>

                <p class="text-muted">
                    Create your first store.
                </p>

                <a href="{{ route('admin.stores.create') }}"
                   class="btn btn-primary">

                    <i class="fa fa-plus"></i>
                    Add Store

                </a>

            </div>

        @endif

    </div>

</div>

@endsection