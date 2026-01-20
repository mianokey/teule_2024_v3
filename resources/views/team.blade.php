@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="page-title-area title-bg-eleven">
  <div class="d-table">
    <div class="d-table-cell">
      <div class="container">
        <div class="title-item">
          <h2>MEET THE TEAM</h2>
          <ul>
            <li>
              <a href="{{route('home')}}">Home</a>
            </li>
            <li>
              <span>Team</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<section class="team-area ptb-100">
  <div class="container">
    <div class="row">
      @foreach($processedMembers as $member)
      <div class="col-sm-6 col-lg-3">
        <div class="team-item">
          <div class="top">
            <img src="{{ asset($member['img_url'] ?? 'uploads/avatar.png') }}"
              alt="{{ $member['name'] ?? 'Team member' }} image" />
          </div>

          <div class="bottom">
            <h3>{{ $member['name'] ?? 'Unnamed' }}</h3>
            <span>{{ $member['position'] ?? '—' }}</span>
          </div>
        </div>
      </div>
      @endforeach

    </div>
  </div>
</section>
<x-footer></x-footer>
@endsection