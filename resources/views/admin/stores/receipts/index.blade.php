@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h6 class="card-title mb-0">
                Store Receiving
            </h6>
            <small class="text-muted">
                Record and manage goods received into Stores.
            </small>
        </div>

        <a href="{{ route('admin.store-receipts.create') }}"
           class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            New Store Receipt
        </a>
    </div>

    <x-message></x-message>

    <div class="card-body">

        @if($receipts->count())

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Receipt No.</th>
                            <th>Store</th>
                            <th>Source</th>
                            <th>Supplier / Reference</th>
                            <th>Received Date</th>
                            <th>Status</th>
                            <th>Received By</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($receipts as $receipt)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $receipt->receipt_number }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $receipt->store->name ?? '—' }}
                                </td>

                                <td>

                                    @if($receipt->source_type === 'DONATION')

                                        <span class="badge bg-info">
                                            Donation
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            Purchase
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @if($receipt->source_type === 'DONATION')

                                        {{ $receipt->donation
                                            ? 'Donation #' . $receipt->donation->id
                                            : '—' }}

                                    @else

                                        {{ $receipt->supplier_name ?: '—' }}

                                        @if($receipt->supplier_reference)
                                            <br>
                                            <small class="text-muted">
                                                Ref:
                                                {{ $receipt->supplier_reference }}
                                            </small>
                                        @endif

                                    @endif

                                </td>

                                <td>
                                    {{ $receipt->received_date
                                        ? $receipt->received_date->format('d/m/Y')
                                        : '—' }}
                                </td>

                                <td>

                                    @switch($receipt->status)

                                        @case('DRAFT')
                                            <span class="badge bg-warning text-dark">
                                                Draft
                                            </span>
                                            @break

                                        @case('POSTED')
                                            <span class="badge bg-success">
                                                Posted
                                            </span>
                                            @break

                                        @case('CANCELLED')
                                            <span class="badge bg-danger">
                                                Cancelled
                                            </span>
                                            @break

                                        @default
                                            <span class="badge bg-secondary">
                                                {{ $receipt->status }}
                                            </span>

                                    @endswitch

                                </td>

                                <td>
                                    {{ $receipt->receivedBy->name ?? '—' }}
                                </td>

                                <td class="text-end">

                                    <a href="{{ route(
                                        'admin.store-receipts.show',
                                        $receipt
                                    ) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="View">

                                        <i class="fas fa-eye"></i>

                                    </a>

                                    @if($receipt->status === 'DRAFT')

                                        <a href="{{ route(
                                            'admin.store-receipts.edit',
                                            $receipt
                                        ) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit">

                                            <i class="fas fa-edit"></i>

                                        </a>

                                        <form
                                            action="{{ route(
                                                'admin.store-receipts.destroy',
                                                $receipt
                                            ) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm(
                                                'Delete this draft store receipt?'
                                            );"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete"
                                            >
                                                <i class="fas fa-trash"></i>
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

                <div class="mb-3">
                    <i class="fas fa-box-open fa-3x text-muted"></i>
                </div>

                <h5>No Store Receipts Yet</h5>

                <p class="text-muted mb-4">
                    No goods have been recorded as received into Stores yet.
                </p>

                <a href="{{ route('admin.store-receipts.create') }}"
                   class="btn btn-primary">

                    <i class="fas fa-plus me-1"></i>
                    Create First Store Receipt

                </a>

            </div>

        @endif

    </div>

</div>

@endsection