@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h6 class="card-title mb-0">
            Store Units
        </h6>

        <a href="{{ route('admin.store-units.create') }}"
           class="btn btn-primary">

            <i class="fa fa-plus"></i>
            Add Unit

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

        @if($units->count())

            <div class="table-responsive">

                <table class="table table-striped table-bordered">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Unit</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($units as $unit)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <strong>{{ $unit->name }}</strong>
                                </td>

                                <td>
                                    <span class="badge badge-info">
                                        {{ $unit->code }}
                                    </span>
                                </td>

                                <td>
                                    {{ $unit->description ?: '—' }}
                                </td>

                                <td>
                                    {{ $unit->items_count }}
                                </td>

                                <td>

                                    @if($unit->is_active)

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

                                    <a href="{{ route('admin.store-units.show', $unit) }}"
                                       class="btn btn-sm btn-info">

                                        <i class="fa fa-eye"></i>

                                    </a>

                                    <a href="{{ route('admin.store-units.edit', $unit) }}"
                                       class="btn btn-sm btn-warning">

                                        <i class="fa fa-edit"></i>

                                    </a>

                                    <form action="{{ route('admin.store-units.toggle-status', $unit) }}"
                                          method="POST"
                                          class="d-inline">

                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="btn btn-sm {{ $unit->is_active ? 'btn-secondary' : 'btn-success' }}"
                                                onclick="return confirm('{{ $unit->is_active ? 'Deactivate this unit?' : 'Activate this unit?' }}')">

                                            <i class="fa {{ $unit->is_active ? 'fa-ban' : 'fa-check' }}"></i>

                                        </button>

                                    </form>

                                    @if($unit->items_count == 0)

                                        <form action="{{ route('admin.store-units.destroy', $unit) }}"
                                              method="POST"
                                              class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this unit?')">

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

                <i class="fa fa-balance-scale fa-3x text-muted mb-3"></i>

                <h5>No Store Units</h5>

                <p class="text-muted">
                    Create your first store unit.
                </p>

                <a href="{{ route('admin.store-units.create') }}"
                   class="btn btn-primary">

                    <i class="fa fa-plus"></i>
                    Add Unit

                </a>

            </div>

        @endif

    </div>

</div>

@endsection