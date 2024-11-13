@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="page-title-area title-bg-eight">
  <div class="d-table">
    <div class="d-table-cell">
      <div class="container">
        <div class="title-item">
          <h2>APPLICATIONS</h2>
          <ul>
            <li>
              <a href="{{route('home')}}">Home</a>
            </li>
            <li>
              <span>Apply for Internship/Attachment </span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="about-area pt-100 pb-70">
  <div class="container">
    <div class="row d-flex justify-content-center align-items-center">

    <div class="col-lg-8 order-md-2">
    <div class="contact-area pb-70">
          <div class="container">
          <h4>Apply for Internship/Attachment</h4>
          <hr>
          <h5 class="text-center">send applications to hr@teulekenya.org</h5>
              <div class="col-md-6" id="spons-content" style="display: none;">
                <div class="popover-content-inner">
                  <h4 class="title"><b><u>What is required?</u></b></h4>
                  <p>
                    In order to apply for internship/Attachment kindly attach the following documents :
                  <ul>
                    <li>Colored National ID</li>
                    <li>Official letter from school</li>
                    <li>Official letter from church</li>
                    <li>Medical certificate</li>
                  </ul>
                  </p>

                </div>
              </div>
          </div>
    </div>

    </div>

      <!-- <div class="col-lg-8 order-md-2">
        <div class="contact-area pb-70">
          <div class="container">
            <form id="contactForm" class="ajax-form" method="post" action="{{route('apply_intern_post')}}">
              <h2>Apply for Internship/Attachment</h2>
              <div class="col-md-6" id="spons-content" style="display: none;">
                <div class="popover-content-inner">
                  <h4 class="title"><b><u>What is required?</u></b></h4>
                  <p>
                    In order to apply for internship/Attachment kindly attach the following documents :
                  <ul>
                    <li>Colored National ID</li>
                    <li>Official letter from school</li>
                    <li>Official letter from church</li>
                    <li>Medical certificate</li>
                  </ul>
                  </p>

                </div>
              </div>

              <div id="sposn-fancybox" data-fancybox="popover" data-src="#spons-content" class="sponsorship-nature">
                <p style="text-align: left important;">What is required?
                  <i class="icon  fa fa-info-circle "></i>
                </p>
              </div>
              @csrf

              
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
               @if (session('success'))
              <div class="alert alert-success">
                {{ session('success') }}
              </div>
              @endif
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
                    <input type="text" name="name" id="name" class="form-control" placeholder="Full Name" required data-error="Please enter your name" />
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
                      <i class="icofont-comment"></i>
                      Attach Files
                    </label>
                    <input type="file" name="attachments[]" id="fileInput" multiple required data-error="Please select one or more files" class="form-control" placeholder="Attach Files" required data-error="Please select one or more files" />
                  </div>
                </div>


                <div class="col-lg-12">
                  <button type="submit" class="btn common-btn mt-3">Send Application</button>
                  <div id="msgSubmit" class="h3 text-center hidden"></div>
                  <div class="clearfix"></div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div> -->

    </div>
  </div>
</div>
<x-footer></x-footer>
@endsection