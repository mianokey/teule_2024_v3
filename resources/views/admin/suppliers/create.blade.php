
@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fas fa-truck"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <i class="fas fa-chevron-right"></i>
                    <a href="{{ route('admin.suppliers.index') }}">Suppliers</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Add Supplier</span>
                </div>

                <h1 class="requisition-page-title">Add Supplier</h1>

                <p class="requisition-page-subtitle">
                    Register a new supplier for purchasing and store LPOs.
                </p>
            </div>

        </div>

    </div>

    <x-message></x-message>

    <form action="{{ route('admin.suppliers.store') }}" method="POST">
        @csrf

        @include('admin.suppliers._form')

    </form>

</div>

@endsection