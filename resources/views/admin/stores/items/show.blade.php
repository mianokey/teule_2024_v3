@extends('layouts.admin')

@section('content')

<div class="row">

    {{-- Item Details --}}
    <div class="col-lg-5 mb-4">

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h6 class="card-title mb-0">
                    Store Item Details
                </h6>

                <a href="{{ route('admin.store-items.edit', $storeItem) }}"
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
                        <th>Category</th>
                        <td>{{ $storeItem->category->name ?? '—' }}</td>
                    </tr>

                    <tr>
                        <th>Unit</th>
                        <td>
                            {{ $storeItem->unit->name ?? '—' }}

                            @if($storeItem->unit)
                                ({{ $storeItem->unit->code }})
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>SKU</th>
                        <td>{{ $storeItem->sku ?: '—' }}</td>
                    </tr>

                    <tr>
                        <th>Item Type</th>
                        <td>

                            @if($storeItem->item_type === 'CONSUMABLE')

                                <span class="badge bg-primary">
                                    Consumable
                                </span>

                            @elseif($storeItem->item_type === 'RETURNABLE')

                                <span class="badge bg-info">
                                    Returnable
                                </span>

                            @else

                                <span class="badge bg-warning text-dark">
                                    Asset
                                </span>

                            @endif

                        </td>
                    </tr>

                    <tr>
                        <th>Reorder Level</th>
                        <td>
                            {{ number_format((float) $storeItem->reorder_level, 3) }}
                        </td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>

                            @if($storeItem->is_active)

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

                @if($storeItem->description)

                    <hr>

                    <strong>Description</strong>

                    <p class="text-muted mt-2 mb-0">
                        {{ $storeItem->description }}
                    </p>

                @endif

            </div>

        </div>

    </div>


    {{-- Stock by Store --}}
    <div class="col-lg-7 mb-4">

        <div class="card">

            <div class="card-header">

                <h6 class="card-title mb-0">
                    Current Stock by Store
                </h6>

            </div>

            <div class="card-body">

                @if($storeItem->stocks->count())

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

                                @foreach($storeItem->stocks as $stock)

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

                    <div class="text-center py-4">

                        <p class="text-muted mb-0">
                            No stock has been received for this item yet.
                        </p>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>
<div class="card mb-4">

    <div class="card-header d-flex justify-content-between align-items-center">

        <div>
            <h6 class="card-title mb-0">
                Item Variants
            </h6>

            <small class="text-muted">
                Optional variations for {{ $storeItem->name }}
            </small>
        </div>

        <a href="{{ route('admin.store-item-variants.create', $storeItem) }}"
           class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
            Add Variant
        </a>

    </div>

    <div class="card-body">

        @if($storeItem->variants->count())

            <div class="table-responsive">

                <table class="table table-bordered table-hover mb-0">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Variant</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($storeItem->variants as $variant)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $variant->name }}
                                    </strong>

                                    @if($variant->description)
                                        <br>
                                        <small class="text-muted">
                                            {{ Str::limit($variant->description, 60) }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    {{ $variant->code ?: '—' }}
                                </td>

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

                                <td>

                                    <div class="d-flex gap-1">

                                        <a href="{{ route('admin.store-item-variants.show', [$storeItem, $variant]) }}"
                                           class="btn btn-sm btn-info">
                                            View
                                        </a>

                                        <a href="{{ route('admin.store-item-variants.edit', [$storeItem, $variant]) }}"
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="text-center py-4">

                <p class="text-muted mb-3">
                    This item does not have any variants.
                </p>

                <a href="{{ route('admin.store-item-variants.create', $storeItem) }}"
                   class="btn btn-outline-primary">
                    Add First Variant
                </a>

            </div>

        @endif

    </div>

</div>


<div class="d-flex justify-content-between">

    <a href="{{ route('admin.store-items.index') }}"
       class="btn btn-secondary">
        Back to Store Items
    </a>

    <form method="POST"
          action="{{ route('admin.store-items.toggle-status', $storeItem) }}">

        @csrf
        @method('PATCH')

        <button type="submit"
                class="btn {{ $storeItem->is_active ? 'btn-secondary' : 'btn-success' }}">

            {{ $storeItem->is_active ? 'Deactivate Item' : 'Activate Item' }}

        </button>

    </form>

</div>

@endsection