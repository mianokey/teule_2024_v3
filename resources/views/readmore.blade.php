@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="page-title-area title-bg-eleven">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="container">
                <div class="title-item">
                    <h2>{{ $documentName }}</h2>
                    <ul>
                        <li>
                            <a href="{{ route('home') }}">Home</a>
                        </li>
                        {{-- test --}}
                        <li>
                            <span>{{ $documentName }}</Title></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="team-area ptb-100">
    <div class="container">
        @if ($pdfUrl)
            <iframe src="{{ $pdfUrl }}" width="100%" height="800px"></iframe>
        @else
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">404 Error</h4>
                <p>The requested PDF *{{ $documentName }}* file was not found.</p>
            </div>
        @endif
    </div>
</section>

<x-footer></x-footer>
@endsection
