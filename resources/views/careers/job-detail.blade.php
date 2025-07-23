@extends('layouts.app')

@section('meta')
    <meta name="description" content="{{ $job['description'] ?? 'Job opportunity at Teule Kenya' }}">
    <meta property="og:title" content="{{ $job['title'] ?? 'Job Opportunity' }} - Teule Kenya">
    <meta property="og:description" content="{{ $job['description'] ?? 'Join our team at Teule Kenya' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    <meta property="og:image" content="{{asset('assets/img/logo192.png')}}">
    <meta name="twitter:card" content="{{asset('assets/img/logo192.png')}}">
@endsection

@section('content')
    <x-header></x-header>
    <x-navbar></x-navbar>

    <div class="page-title-area title-bg-eight">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="container">
                    <div class="title-item">
                        <h2>CAREERS</h2>
                        <ul>
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><span>{{ $job['title'] ?? 'Job Details' }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="about-area pb-5">
        <div class="container mt-5">
            <div class="card shadow pb-5">
                <div class="card-header text-white" style="background-color: var(--main-color--green);">
                    <h5 class="mb-0">Job Title: {{ $job['title'] ?? 'Untitled' }}</h5>
                    <p class="mb-0"><strong>Application Deadline:</strong> {{ $job['deadline'] ?? 'N/A' }}</p>

                    @if (!empty($job['email']))
                        <p class="mb-0">
                            <strong>Send Applications to:</strong> 
                            <a href="mailto:{{ $job['email'] }}" class="text-white">{{ $job['email'] }}</a>
                            @if (!empty($job['cc']))
                                <strong> cc: </strong>
                                <a href="mailto:{{ $job['cc'] }}" class="text-white">{{ $job['cc'] }}</a>
                            @endif
                        </p>
                    @endif
                </div>

                <div class="card-body">
                    <p>{{ $job['description'] ?? 'No description available.' }}</p>
                </div>

                <div class="card-footer text-center d-flex flex-column gap-2">
                    @if (!empty($job['pdf']))
                        <a class="common-btn mb-2" style="border-color:var(--main-color--green);" href="{{ route('readmore', ['documentName' => $job['pdf']]) }}">
                            <i class="fa fa-download"></i> {{ $job['title'] }} Job Advert
                        </a>
                    @endif

                    <button onclick="copyJobUrl(window.location.href, this)" class="common-btn btn-outline-success" style="border-color:var(--main-color--green); color: var(--main-color--green);">
                        <i class="fa fa-link"></i> Copy Job Link
                    </button>
                </div>
            </div>
        </div>
    </div>

    <x-footer></x-footer>
@endsection

@push('scripts')
<script>
function copyJobUrl(url, btn) {
    navigator.clipboard.writeText(url).then(() => {
        btn.innerHTML = '<i class="fa fa-check text-success"></i> Link Copied!';
        setTimeout(() => {
            btn.innerHTML = '<i class="fa fa-link"></i> Copy Job Link';
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy:', err);
        btn.innerHTML = '<i class="fa fa-times text-danger"></i> Failed!';
    });
}
</script>
@endpush
