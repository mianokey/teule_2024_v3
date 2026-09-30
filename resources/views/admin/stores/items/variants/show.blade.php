@extends('layouts.admin')

@section('content')

<div class="row">

    <div class="col-lg-5 mb-4">

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h6 class="card-title mb-0">
                    Variant Details
                </h6>

                <a href="{{ route('admin.store-item-variants.edit', [$storeItem, $variant]) }}"
                   class="btn btn-sm btn-warning">
                    Edit
                </a>

            </div>

            <div class="card-body">

                <table class="table table-borderless">

                    <tr>
                        <th width="40%">Item</th>
                        <td>{{ $storeItem->name }}</td>
                    </tr>

                    <tr>
                        <th>Variant</th>
                        <td>{{ $variant->name }}</td>
                    </tr>

                    <tr>
                        <th>Code</th>
                        <td>{{ $variant->code ?: '—' }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>

                            @if($variant->is_active)

                                <span class="badge bg-success">
                                    Active
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    Inactive
                                </span>

                            @endif

                        </td>
                    </tr>

                </table>

                @if($variant->description)

                    <hr>

                    <strong>Description</strong>

                    <p class="text-muted mt-2">
                        {{ $variant->description }}
                    </p>

                @endif

            </div>

        </div>

    </div>


    <div class="col-lg-7 mb-4">

        <div class="card">

            <div class="card-header">
                <h6 class="card-title mb-0">
                    Stock by Store
                </h6>
            </div>

            <div class="card-body">

                @if($variant->stocks->count())

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead>
                                <tr>
                                    <th>Store</th>
                                    <th>Code</th>
                                    <th class="text-end">Quantity</th>
                                    <th>Last Movement</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($variant->stocks as $stock)

                                    <tr>

                                        <td>
                                            {{ $stock->store->name ?? '—' }}
                                        </td>

                                        <td>
                                            {{ $stock->store->code ?? '—' }}
                                        </td>

                                        <td class="text-end">
                                            <strong>
                                                {{ number_format((float) $stock->quantity, 3) }}
                                            </strong>
                                        </td>

                                        <td>
                                            {{ $stock->last_movement_at
                                                ? $stock->last_movement_at->format('d M Y H:i')
                                                : '—' }}
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <p class="text-muted text-center mb-0">
                        No stock has been received for this variant yet.
                    </p>

                @endif

            </div>

        </div>

    </div>

</div>

<div class="d-flex justify-content-between">

    <a href="{{ route('admin.store-item-variants.index', $storeItem) }}"
       class="btn btn-secondary">
        Back to Variants
    </a>

    <form method="POST"
          action="{{ route('admin.store-item-variants.toggle-status', [$storeItem, $variant]) }}">

        @csrf
        @method('PATCH')

        <button type="submit"
                class="btn {{ $variant->is_active ? 'btn-secondary' : 'btn-success' }}">

            {{ $variant->is_active ? 'Deactivate Variant' : 'Activate Variant' }}

        </button>

    </form>

</div>

@endsection