@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="page-title-area title-bg-eleven">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="container">
                <div class="title-item">
                    <h2>ERROR-UNABLE TO ACCESS RESOURCE</h2>
                    <ul>
                        <li>
                            <a href="{{ route('home') }}">Home</a>
                        </li>
                        <li>
                            <span>UNABLE TO ACCESS RESOURCE</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="team-area ptb-100">
    <div class="container text-center">
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">404 Error</h4>
            <p>The requested resource was not found. Kindly contact admin (info@teulekenya.org) for further assistance.</p>
            @if ($errorMessage)
                <p>{{$errorMessage}}</p>
            @endif
            <button onclick="window.history.back()" class="btn btn-secondary">Go Back</button>
        </div>
    </div>
</section>

<x-footer></x-footer>
@endsection
