@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h6 class="card-title mb-0">
                Store Items
            </h6>
            <small class="text-muted">
                Manage items held in Teule Stores
            </small>
        </div>

        <a href="{{ route('admin.store-items.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
            Add Store Item
        </a>
    </div>

    <div class="card-body">

        <x-message></x-message>

        @if($items->count())

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th>Type</th>
                            <th>SKU</th>
                            <th>Stock</th>
                            <th>Reorder Level</th>
                            <th>Status</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($items as $item)

                            @php
                                $totalStock = $item->stocks->sum(function ($stock) {
                                    return (float) $stock->quantity;
                                });
                            @endphp

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <strong>{{ $item->name }}</strong>

                                    @if($item->description)
                                        <br>
                                        <small class="text-muted">
                                            {{ Str::limit($item->description, 60) }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    {{ $item->category->name ?? '—' }}
                                </td>

                                <td>
                                    {{ $item->unit->code ?? $item->unit->name ?? '—' }}
                                </td>

                                <td>

                                    @if($item->item_type === 'CONSUMABLE')

                                        <span class="badge bg-primary">
                                            Consumable
                                        </span>

                                    @elseif($item->item_type === 'RETURNABLE')

                                        <span class="badge bg-info">
                                            Returnable
                                        </span>

                                    @elseif($item->item_type === 'ASSET')

                                        <span class="badge bg-warning text-dark">
                                            Asset
                                        </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $item->sku ?: '—' }}
                                </td>

                                <td>
                                    <strong>
                                        {{ number_format($totalStock, 3) }}
                                    </strong>
                                </td>

                                <td>
                                    {{ number_format((float) $item->reorder_level, 3) }}
                                </td>

                                <td>

                                    @if($item->is_active)

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

                                        <a href="{{ route('admin.store-items.show', $item) }}"
                                           class="btn btn-sm btn-info">
                                            View
                                        </a>

                                        <a href="{{ route('admin.store-items.edit', $item) }}"
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        <form method="POST"
                                              action="{{ route('admin.store-items.toggle-status', $item) }}">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                    class="btn btn-sm {{ $item->is_active ? 'btn-secondary' : 'btn-success' }}">
                                                {{ $item->is_active ? 'Disable' : 'Enable' }}
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="text-center py-5">

                <h5>No Store Items Yet</h5>

                <p class="text-muted">
                    Start by adding the first item to your Store Items master list.
                </p>

                <a href="{{ route('admin.store-items.create') }}"
                   class="btn btn-primary">
                    Add Store Item
                </a>

            </div>

        @endif

    </div>

</div>

@endsection