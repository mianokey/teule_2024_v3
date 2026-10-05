```blade
@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fas fa-truck"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <a href="{{ route('admin.suppliers.index') }}">
                        Suppliers
                    </a>
                    <i class="fas fa-chevron-right"></i>
                    <span>{{ $supplier->supplier_code }}</span>
                </div>

                <h1 class="requisition-page-title">
                    {{ $supplier->name }}
                </h1>

                <p class="requisition-page-subtitle">
                    Supplier details and account information.
                </p>
            </div>

        </div>

        <div class="requisition-header-right">
            <div class="requisition-page-actions">

                <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                   class="requisition-add-button">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>

                <a href="{{ route('admin.suppliers.index') }}"
                   class="requisition-cancel-button">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>

            </div>
        </div>

    </div>


    {{-- ============================================================
         FLASH MESSAGES
         ============================================================ --}}
    <x-message></x-message>


    {{-- ============================================================
         SUPPLIER STATUS
         ============================================================ --}}
    <div class="requisition-state-panel">

        <div class="requisition-state-main">

            <div class="requisition-state-icon">
                @if($supplier->is_active)
                    <i class="fas fa-check-circle"></i>
                @else
                    <i class="fas fa-ban"></i>
                @endif
            </div>

            <div class="requisition-state-text">

                <strong>
                    {{ $supplier->is_active ? 'Active Supplier' : 'Inactive Supplier' }}
                </strong>

                <span>
                    Supplier Code: {{ $supplier->supplier_code }}
                </span>

            </div>

        </div>

        <div class="requisition-state-actions">

            @if($supplier->is_active)

                <form method="POST"
                      action="{{ route('admin.suppliers.deactivate', $supplier) }}">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                            class="requisition-cancel-button"
                            onclick="return confirm('Deactivate this supplier?')">
                        <i class="fas fa-toggle-off"></i>
                        Deactivate
                    </button>
                </form>

            @else

                <form method="POST"
                      action="{{ route('admin.suppliers.activate', $supplier) }}">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                            class="requisition-add-button">
                        <i class="fas fa-toggle-on"></i>
                        Activate
                    </button>
                </form>

            @endif

        </div>

    </div>


    {{-- ============================================================
         SUPPLIER DETAILS
         ============================================================ --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <span class="requisition-section-icon">
                    <i class="fas fa-building"></i>
                </span>

                <div>
                    <h5>Supplier Details</h5>
                    <p>Supplier identification and contact information.</p>
                </div>

            </div>

        </div>


        <div class="row">

            <div class="col-md-3">
                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">
                        Supplier Code
                    </div>

                    <div class="requisition-detail-value fw-semibold">
                        {{ $supplier->supplier_code }}
                    </div>
                </div>
            </div>


            <div class="col-md-5">
                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">
                        Supplier Name
                    </div>

                    <div class="requisition-detail-value fw-semibold">
                        {{ $supplier->name }}
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">
                        Contact Person
                    </div>

                    <div class="requisition-detail-value">
                        {{ $supplier->contact_person ?: '—' }}
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">
                        Phone
                    </div>

                    <div class="requisition-detail-value">
                        {{ $supplier->phone ?: '—' }}
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">
                        Email
                    </div>

                    <div class="requisition-detail-value">
                        {{ $supplier->email ?: '—' }}
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">
                        Tax PIN
                    </div>

                    <div class="requisition-detail-value">
                        {{ $supplier->tax_pin ?: '—' }}
                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">
                        Payment Terms
                    </div>

                    <div class="requisition-detail-value">
                        {{ $supplier->payment_terms ?: '—' }}
                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">
                        Address
                    </div>

                    <div class="requisition-detail-value">
                        {!! nl2br(e($supplier->address ?: '—')) !!}
                    </div>
                </div>
            </div>

        </div>

    </div>


    {{-- ============================================================
         ACCOUNT SUMMARY
         ============================================================ --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <span class="requisition-section-icon">
                    <i class="fas fa-calculator"></i>
                </span>

                <div>
                    <h5>Supplier Account</h5>
                    <p>Current supplier account position.</p>
                </div>

            </div>

        </div>


        <div class="requisition-summary-strip">

            <div class="requisition-summary-item">
                <div class="requisition-summary-label">
                    Opening Balance
                </div>

                <div class="requisition-summary-value">
                    {{ number_format((float) $supplier->opening_balance, 2) }}
                </div>
            </div>


            <div class="requisition-summary-item">
                <div class="requisition-summary-label">
                    Total Debit
                </div>

                <div class="requisition-summary-value">
                    {{ number_format($totalDebit, 2) }}
                </div>
            </div>


            <div class="requisition-summary-item">
                <div class="requisition-summary-label">
                    Total Credit
                </div>

                <div class="requisition-summary-value">
                    {{ number_format($totalCredit, 2) }}
                </div>
            </div>


            <div class="requisition-summary-item">
                <div class="requisition-summary-label">
                    Current Balance
                </div>

                <div class="requisition-summary-value">
                    {{ number_format($currentBalance, 2) }}
                </div>
            </div>


            <div class="requisition-summary-item">
                <div class="requisition-summary-label">
                    Credit Limit
                </div>

                <div class="requisition-summary-value">
                    {{ number_format((float) $supplier->credit_limit, 2) }}
                </div>
            </div>


            <div class="requisition-summary-item">
                <div class="requisition-summary-label">
                    Transactions
                </div>

                <div class="requisition-summary-value">
                    {{ $supplier->accountTransactions->count() }}
                </div>
            </div>

        </div>

    </div>


    {{-- ============================================================
         ACCOUNT TRANSACTIONS
         ============================================================ --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <span class="requisition-section-icon">
                    <i class="fas fa-list"></i>
                </span>

                <div>
                    <h5>
                        Account Transactions
                        <span class="requisition-count-badge">
                            {{ $supplier->accountTransactions->count() }}
                        </span>
                    </h5>

                    <p>
                        Supplier account activity and balances.
                    </p>
                </div>

            </div>

        </div>


        @if($supplier->accountTransactions->count())

            <div class="requisition-list-table-wrapper">

                <table class="requisition-list-table">

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Description</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>

                    <tbody>

                    @foreach($supplier->accountTransactions as $transaction)

                        <tr>

                            <td>
                                {{ optional($transaction->transaction_date)->format('d M Y') }}
                            </td>

                            <td>
                                {{ $transaction->transaction_type ?: '—' }}
                            </td>

                            <td>
                                {{ $transaction->reference_number ?: '—' }}
                            </td>

                            <td>
                                {{ $transaction->description ?: '—' }}
                            </td>

                            <td class="text-end">

                                @if((float) $transaction->debit > 0)
                                    {{ number_format((float) $transaction->debit, 2) }}
                                @else
                                    —
                                @endif

                            </td>

                            <td class="text-end">

                                @if((float) $transaction->credit > 0)
                                    {{ number_format((float) $transaction->credit, 2) }}
                                @else
                                    —
                                @endif

                            </td>

                            <td class="text-end fw-semibold">
                                {{ number_format((float) $transaction->balance, 2) }}
                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="requisition-table-empty">

                <div class="requisition-empty-icon">
                    <i class="fas fa-receipt"></i>
                </div>

                <h5>
                    No account transactions
                </h5>

                <p>
                    No supplier account transactions have been recorded yet.
                </p>

            </div>

        @endif

    </div>


    {{-- ============================================================
         NOTES
         ============================================================ --}}
    @if($supplier->notes)

        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <span class="requisition-section-icon">
                        <i class="fas fa-sticky-note"></i>
                    </span>

                    <div>
                        <h5>Notes</h5>
                        <p>Additional information about this supplier.</p>
                    </div>

                </div>

            </div>

            <div class="requisition-detail-value">
                {!! nl2br(e($supplier->notes)) !!}
            </div>

        </div>

    @endif


    {{-- ============================================================
         DELETE SUPPLIER
         ============================================================ --}}
    @if($supplier->accountTransactions->count() === 0)

        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading text-danger">

                    <span class="requisition-section-icon">
                        <i class="fas fa-trash"></i>
                    </span>

                    <div>
                        <h5>Delete Supplier</h5>
                        <p>
                            Permanently remove this supplier record.
                        </p>
                    </div>

                </div>

            </div>


            <div class="requisition-bottom-actions">

                <div class="requisition-bottom-actions-left">

                    <span class="text-muted small">
                        Deletion will be blocked once this supplier is
                        used for an LPO or account transaction.
                    </span>

                </div>

                <div class="requisition-bottom-actions-right">

                    <form method="POST"
                          action="{{ route('admin.suppliers.destroy', $supplier) }}"
                          onsubmit="return confirm('Are you sure you want to permanently delete this supplier?')">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="btn btn-outline-danger">

                            <i class="fas fa-trash"></i>
                            Delete Supplier

                        </button>

                    </form>

                </div>

            </div>

        </div>

    @endif


</div>

@endsection