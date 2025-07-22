@extends('layouts.app')

@section('meta')
    <title>{{ $job['title'] }} – Teule Kenya</title>
    <meta name="description" content="{{ $job['summary'] }}">
    <meta name="keywords" content="{{ $job['tags'] }}">
    <meta property="og:title" content="{{ $job['title'] }} – Teule Kenya">
    <meta property="og:description" content="{{ $job['summary'] }}">
    <meta property="og:image" content="{{ asset($job['image'] ?? 'images/default-job.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $job['title'] }} – Teule Kenya">
    <meta name="twitter:description" content="{{ $job['summary'] }}">
    <meta name="twitter:image" content="{{ asset($job['image'] ?? 'images/default-job.jpg') }}">
@endsection

@section('content')
<x-header />
<x-navbar />

<div class="about-area pb-5">
  <div class="container mt-5">
    <div class="card shadow pb-5">
      <div class="card-header text-white" style="background-color: var(--main-color--green);">
        <h5 class="mb-0">Job Title: {{ $job['title'] }}</h5>
        <p class="mb-0"><strong>Application Deadline:</strong> {{ \Carbon\Carbon::parse($job['deadline'])->toFormattedDateString() }}</p>
        <p class="mb-0">
          <strong>Send Applications to:</strong>
          <a href="mailto:{{ $job['email'] }}" class="text-white">{{ $job['email'] }}</a>
        </p>
      </div>
      <div class="card-body">
        <p>{{ $job['summary'] }}</p>
      </div>
      <div class="card-footer text-center">
        <a class="common-btn" href="{{ asset('documents/' . strtoupper($job['slug']) . '-ADVERT.pdf') }}">
          <i class="fa fa-download"></i> Download Job Advert PDF
        </a>
      </div>
    </div>
  </div>
</div>

<x-footer />
@endsection
