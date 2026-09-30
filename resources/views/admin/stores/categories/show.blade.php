@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h6 class="card-title mb-0">
            Store Item Category
        </h6>

        <div>

            <a href="{{ route('admin.store-categories.edit', $storeCategory) }}"
               class="btn btn-warning">
                <i class="fa fa-edit"></i>
                Edit
            </a>

            <a href="{{ route('admin.store-categories.index') }}"
               class="btn btn-secondary">
                Back
            </a>

        </div>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <strong>Category Name</strong>

                <p>
                    {{ $storeCategory->name }}
                </p>

            </div>

            <div class="col-md-3">

                <strong>Status</strong>

                <p>

                    @if($storeCategory->is_active)

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

            <div class="col-md-3">

                <strong>Items</strong>

                <p>
                    {{ $storeCategory->items_count }}
                </p>

            </div>

        </div>

        <hr>

        <strong>Description</strong>

        <p class="mt-2">
            {{ $storeCategory->description ?: 'No description provided.' }}
        </p>

    </div>

</div>

@endsection