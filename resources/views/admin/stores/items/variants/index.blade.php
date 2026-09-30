@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <div>
            <h6 class="card-title mb-0">
                {{ $storeItem->name }} — Variants
            </h6>

            <small class="text-muted">
                Manage variations of this store item.
            </small>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('admin.store-items.show', $storeItem) }}"
               class="btn btn-secondary btn-sm">
                Back to Item
            </a>

            <a href="{{ route('admin.store-item-variants.create', $storeItem) }}"
               class="btn btn-primary btn-sm">
                Add Variant
            </a>

        </div>

    </div>

    <div class="card-body">

        <x-message></x-message>

        @if($variants->count())

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Variant</th>
                            <th>Code</th>
                            <th>Stock Records</th>
                            <th>Status</th>
                            <th width="220">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($variants as $variant)

                            <tr>

                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    <strong>{{ $variant->name }}</strong>

                                    @if($variant->description)
                                        <br>
                                        <small class="text-muted">
                                            {{ Str::limit($variant->description, 70) }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    {{ $variant->code ?: '—' }}
                                </td>

                                <td>
                                    {{ $variant->stocks_count }}
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

                                        <form method="POST"
                                              action="{{ route('admin.store-item-variants.toggle-status', [$storeItem, $variant]) }}">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                    class="btn btn-sm {{ $variant->is_active ? 'btn-secondary' : 'btn-success' }}">

                                                {{ $variant->is_active ? 'Disable' : 'Enable' }}

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

                <h5>No Variants Yet</h5>

                <p class="text-muted">
                    This item does not have any variants.
                </p>

                <a href="{{ route('admin.store-item-variants.create', $storeItem) }}"
                   class="btn btn-primary">
                    Add First Variant
                </a>

            </div>

        @endif

    </div>

</div>

@endsection