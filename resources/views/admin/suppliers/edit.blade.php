@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fas fa-edit"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <i class="fas fa-chevron-right"></i>
                    <a href="{{ route('admin.suppliers.index') }}">Suppliers</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Edit Supplier</span>
                </div>

                <h1 class="requisition-page-title">Edit Supplier</h1>

                <p class="requisition-page-subtitle">
                    Update supplier information and account details.
                </p>
            </div>

        </div>

    </div>

    <x-message></x-message>

    <div class="requisition-state-panel mb-3">
        <div>
            <strong>{{ $supplier->name }}</strong>
            <div class="requisition-list-meta">
                Supplier Code: {{ $supplier->supplier_code }}
            </div>
        </div>
    </div>

    <form action="{{ route('admin.suppliers.update', $supplier) }}"
          method="POST">

        @csrf
        @method('PUT')

        @include('admin.suppliers._form')

    </form>

</div>

@endsection