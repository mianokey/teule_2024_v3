@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h6 class="card-title mb-0">
            Store Unit
        </h6>

        <div>

            <a href="{{ route('admin.store-units.edit', $storeUnit) }}"
               class="btn btn-warning">

                <i class="fa fa-edit"></i>
                Edit

            </a>

            <a href="{{ route('admin.store-units.index') }}"
               class="btn btn-secondary">

                Back

            </a>

        </div>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4">

                <strong>Unit Name</strong>

                <p>
                    {{ $storeUnit->name }}
                </p>

            </div>

            <div class="col-md-4">

                <strong>Unit Code</strong>

                <p>
                    <span class="badge badge-info">
                        {{ $storeUnit->code }}
                    </span>
                </p>

            </div>

            <div class="col-md-4">

                <strong>Status</strong>

                <p>

                    @if($storeUnit->is_active)

                        <span class="badge badge-success">
                            Active
                        </span>

                    @else

                        <span class="badge badge-secondary">
                            Inactive
                        </span>

                    @endif

                </p>

            </div>

        </div>

        <hr>

        <strong>Items Using This Unit</strong>

        <p>
            {{ $storeUnit->items_count }}
        </p>

        <hr>

        <strong>Description</strong>

        <p class="mt-2">
            {{ $storeUnit->description ?: 'No description provided.' }}
        </p>

    </div>

</div>

@endsection