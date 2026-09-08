@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="page-title-area title-bg-eight">
  <div class="d-table">
    <div class="d-table-cell">
      <div class="container">
        <div class="title-item">
          <h2>OUR PROGRAM</h2>
          <ul>
            <li>
              <a href="{{route('home')}}">Home</a>
            </li>
            <li>
              <span>Teule Kenya Programs</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="about-area pt-100 pb-100">
    <div class="container">

        <div class="row mb-5">
            <div class="col-lg-12 text-center">

                <h2 class="fw-bold">
                    Community Based Care Program
                </h2>

                <p class="text-muted">
                    Empowering vulnerable children, strengthening families and transforming communities.
                </p>

            </div>
        </div>

        <div class="row justify-content-center">

            <div class="col-lg-12">

                <div class="need-board">

                    <svg id="needWheel"
                         viewBox="0 0 1200 1200"
                         preserveAspectRatio="xMidYMid meet">

                    </svg>

                </div>

            </div>

        </div>

    </div>
</div>
<x-footer></x-footer>
@endsection