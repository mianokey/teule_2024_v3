@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h6 class="card-title mb-0">
            Store Details
        </h6>

        <div>

            <a href="{{ route('admin.stores.edit', $store) }}"
               class="btn btn-warning">

                <i class="fa fa-edit"></i>
                Edit

            </a>

            <a href="{{ route('admin.stores.index') }}"
               class="btn btn-secondary">

                Back

            </a>

        </div>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4">

                <strong>Store Name</strong>

                <p>
                    {{ $store->name }}
                </p>

            </div>

            <div class="col-md-4">

                <strong>Store Code</strong>

                <p>
                    <span class="badge badge-info">
                        {{ $store->code }}
                    </span>
                </p>

            </div>

            <div class="col-md-4">

                <strong>Status</strong>

                <p>

                    @if($store->is_active)

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

        <strong>Stock Records</strong>

        <p>
            {{ $store->stocks_count }}
        </p>

        <hr>

        <strong>Description</strong>

        <p class="mt-2">
            {{ $store->description ?: 'No description provided.' }}
        </p>

    </div>

</div>

@endsection