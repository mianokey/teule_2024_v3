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
    <div class="card shadow pb-5">
      <div class="card-header text-white" style="background-color: var(--main-color--green);">
        <h5 class="mb-0">Job Title: Center Manager</h5>
        <p class="mb-0">
          <strong>Application Deadline:</strong> March 25, 2025
        </p>
        <p class="mb-0">
          <strong>Send Applications to:</strong> <a href="mailto:info@teulekenya.org" class="text-white">info@teulekenya.org</a>
          <strong>cc:</strong> <a href="mailto:programs@teulekenya.org" class="text-white">programs@teulekenya.org</a>
        </p>
      </div>
      <div class="card-body">
        <p>
          We invite applications from suitable candidates for the position of <b>Center Manager</b>
        </p>
      </div>
      <div class="card-footer text-center">
        <a class="common-btn"  style="border-color:var(--main-color--green);" href="{{ route('readmore', ['documentName' => 'CM _JOB_ADVERT.pdf']) }}"><i class="fa fa-download"></i> Center Manager Job Advert</a>
      </div>
    </div>
    <div style="" class="card shadow pb-5 mt-3">
      <div class="card-header text-white" style="background-color: var(--main-color--green);">
        <h5 class="mb-0">Job Title: Junior Secondary Teacher</h5>
        <p class="mb-0">
          <strong>Application Deadline:</strong> February 27, 2025
        </p>
        <p class="mb-0">
          <strong>Send Applications to:</strong> <a href="mailto:info@teulekenya.org" class="text-white">headteacher@teulekenya.org</a>
        </p>
      </div>
      <div class="card-body">
        <p>
          Teule Leadership Academy is seeking a passionate and dedicated <b> Junior Secondary School</b>(JSS) Teacher to join our team.
        </p>
      </div>
      <div class="card-footer text-center">
        <a class="common-btn"  style="border-color:var(--main-color--green);" href="{{ route('readmore', ['documentName' => 'TLA_JSS.pdf']) }}"><i class="fa fa-download"></i> Juniour Secondary Teacher Job Advert</a>
      </div>
    </div>
  </div>
</div>

<x-footer></x-footer>
@endsection
