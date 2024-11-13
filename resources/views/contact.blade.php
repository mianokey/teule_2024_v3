@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="page-title-area title-bg-eight">
  <div class="d-table">
    <div class="d-table-cell">
      <div class="container">
        <div class="title-item">
          <h2>CONTACT US</h2>
          <ul>
            <li>
              <a href="{{route('home')}}">Home</a>
            </li>
            <li>
              <span>Contact Us</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="about-area pt-100 pb-70">
  <div class="container">
    <div class="row align-items-center">


      <div class="col-lg-8 order-md-2">
        <div class="contact-area pb-70">
          <div class="container">
            <form id="contactForm" class="ajax-form" method="post" action="{{route('contact_message')}}">
              <h2>Let's talk...!</h2>
              @csrf

              <!-- Display the flash message -->
  @if (request()->has('type') && request()->has('message'))
    <div class="flash-message">
      @if (request()->get('type') === 'success')
        <div class="alert alert-success">
          {{ request()->get('message') }}
        </div>
      @elseif (request()->get('type') === 'error')
        <div class="alert alert-danger">
          {{ request()->get('message') }}
        </div>
      @endif
    </div>
  @endif

              <!-- Display success message if it exists in the session -->
              @if (session('success'))
              <div class="alert alert-success">
                {{ session('success') }}
              </div>
              @endif

              <!-- Display validation errors -->
              @if ($errors->any())
              <div class="alert alert-danger">
                <ul>
                  @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
              @endif
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>
                      <i class="icofont-user-alt-3"></i>
                    </label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" required data-error="Please enter your name" />
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>
                      <i class="icofont-ui-email"></i>
                    </label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" required data-error="Please enter your email" />
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>
                      <i class="icofont-ui-call"></i>
                    </label>
                    <input type="text" name="phone_number" id="phone_number" placeholder="Phone" required data-error="Please enter your number" class="form-control" />
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>
                      <i class="icofont-notepad"></i>
                    </label>
                    <input type="text" name="msg_subject" id="msg_subject" class="form-control" placeholder="Subject" required data-error="Please enter your subject" />
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="form-group">
                    <label>
                      <i class="icofont-comment"></i>
                    </label>
                    <textarea name="message" class="form-control" id="message" cols="30" rows="8" placeholder="Write message" required data-error="Write your message"></textarea>
                  </div>
                </div>

                <div class="col-lg-12">
                  <button type="submit" class="btn common-btn mt-3">Send Message</button>
                  <div id="msgSubmit" class="h3 text-center hidden"></div>
                  <div class="clearfix"></div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-4 order-md-1">
        <div class="contact-info-area pt-100 pb-70">
          <div class="container">
            <div class="row">
              <div class="col-sm-7 col-lg-12">
                <div class="contact-info">
                  <i class="icofont-location-pin"></i>
                  <span>Location:</span>
                  <a href="javascript:void(0)">P.o Box 184-00209,Oloitokitok, Kenya</a>
                  <a href="javascript:void(0)">1042 Maple Avenue137 Lisle,IL 60532</a>
                </div>
              </div>
              <div class="col-sm-5 col-lg-12">
                <div class="contact-info">
                  <i class="icofont-ui-call"></i>
                  <span>Phone:</span>
                  <a href="tel:+123456789">+254 721582323</a>
                  <a href="tel:+548658956">EIN #- 47-5102209</a>
                </div>
              </div>
              <div class="col-sm-6 col-lg-12">
                <div class="contact-info">
                  <i class="icofont-ui-email"></i>
                  <span>Email:</span>
                  info@teulekenya.org </br>
                  teuleusa@teulekenya.org
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<x-footer></x-footer>
@endsection