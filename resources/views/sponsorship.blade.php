@php
use Carbon\Carbon;
@endphp
@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="page-title-area title-bg-eleven">
  <div class="d-table">
    <div class="d-table-cell">
      <div class="container">
        <div class="title-item">
          <h2>SPONSOR A CHILD</h2>
          <ul>
            <li>
              <a href="{{route('home')}}">Home</a>
            </li>
            <li>
              <span>sponsorship</span>
            </li>
          </ul>
          <!-- The hidden HTML content that will appear in the popover -->
          <div class="col-md-6" id="spons-content" style="display: none;">
                    <div class="popover-content-inner">
                        <h4 class="title"><b><u>WHAT YOU NEED TO KNOW ABOUT CHILD SPONSORSHIP</u></b></h4>
                        <p>
                            Child Sponsorship is one of the many ways for partners like you
                            to support Teule in serving vulnerable children and their families.
                            When you sponsor a child, you will receive updates on your child's progress and 
                            will have the opportunity to communicate with them.
                            Sponsorship funds are pooled together to provide for the basic needs
                            of all the children in our program and assist in stabilizing vulnerable
                            families in the community.In addition to providing access to quality
                            education your contributions also cover psychosocial support from our
                            counselor and social workers. Each child needs several sponsors to cover the cost of holistic care.
                        </p>
                    </div>
                </div>
          <div id="sposn-fancybox" data-fancybox="popover" data-src="#spons-content" class="sponsorship-nature">
    <p style="color: white !important;" class="text-center">Click here to learn more about our sponsorship
      <i class="icon  fa fa-info-circle "></i>
    </p>
  </div>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="team-area ptb-100">
  <div class="container">
    <div class="row">
      @foreach($children as $child)
      @php
      $child_details = $child->details;
      @endphp
      <div class="col-md-4" id="cont_{{ $child->id }}" style="display: none;">
        <div class="popover-content-inner">
          <div class="row">
            <div class="col-md-4">
              <img src="{{ asset($child->img_url) }}" alt="Profile Image">
            </div>
            <div class="col-md-8">
              <h5 class="title"><b><u>Meet {{ Str::title(strtolower($child->name)) }}</u></b></h5>
              <p><b>Date of Birth:</b> {{ $child->dob }}</p>
              <p><b>Age:</b> {{ $child->age }}</p>
              <h6><b><u>Other Details:</u></b></h6>
              @if ($child->details)
                    @foreach ($child->details as $detail)
                    @if ($detail->key !== 'current_grade' && $detail->key !== 'sponsors')
                    <p><b>{{ $detail->key }}:</b> {{ $detail->value }}</p>
                    @endif
                    @endforeach
                @else
                    <p>no details </p>
                @endif
            </div>
          </div>
          <a href="{{ route('donate') }}"><button class="common-btn">Sponsor {{ Str::ucfirst(strtolower($child->name)) }}</button></a>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="team-item">
          <div class="top">

            <img  src="{{ asset($child->img_url) }}" alt="{{ $child->name }} image" />
          </div>
          <div class="bottom">
            <h4>{{ Str::title(strtolower($child->name)) }}</h4>
            <span class="mb-2">{{ $child->age}}</span>
            <span class="mb-2"><b class="text-dark">CATEGORY: </b>{{ $child->status}}</span>
            <button data-fancybox="popover" data-src="#cont_{{ $child->id }}" class="sponsor_btn">More details/Sponsor</button>
          </div>
        </div>
      </div>
      @endforeach
{{ $children->links() }}
      
      
    </div>
  </div>
</section>

<x-footer></x-footer>
@endsection