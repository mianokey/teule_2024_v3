@extends('layouts.app')

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
            <li>
              <a href="{{ route('home') }}">Home</a>
            </li>
            <li>
              <span>Apply for available positions</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>




<div class="about-area pb-5">
    <div class="container mt-5">
        @php
            use Illuminate\Support\Str;
            $jobs = include resource_path('data/jobs.php');
        @endphp

        @foreach ($jobs as $slug => $job)
        <div class="card shadow pb-5 mb-4">
            <div class="card-header text-white" style="background-color: var(--main-color--green);">
                <h5 class="mb-0">Job Title: {{ $job['title'] }}</h5>
                <p class="mb-0"><strong>Application Deadline:</strong> {{ $job['deadline'] }}</p>

                @if(isset($job['emails']))
                <p class="mb-0">
                    <strong>Send Applications to:</strong>
                    <a href="mailto:{{ $job['emails']['to'] }}" class="text-white">{{ $job['emails']['to'] }}</a>
                    @if(isset($job['emails']['cc']))
                        <strong>cc:</strong> <a href="mailto:{{ $job['emails']['cc'] }}" class="text-white">{{ $job['emails']['cc'] }}</a>
                    @endif
                </p>
                @endif
            </div>

            <div class="card-body">
                <p>{{ Str::limit($job['description'], 200) }}</p>
            </div>

            <div class="card-footer text-center d-flex flex-column gap-2">
                @if(isset($job['pdf']))
                <a class="common-btn mb-2" style="border-color:var(--main-color--green);" href="{{ route('readmore', ['documentName' => $job['pdf']]) }}">
                    <i class="fa fa-download"></i> {{ $job['title'] }} Job Advert
                </a>
                @endif

                <button onclick="copyJobUrl('{{ route('careers.show', ['slug' => $slug]) }}', this)" class="common-btn btn-outline-success" style="border-color:var(--main-color--green); color: var(--main-color--green);">
                    <i class="fa fa-link"></i> Copy Job Link
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<x-footer />

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
@endsection
