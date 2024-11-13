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
              <span>Apply as a volunteer </span>
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

    <div class="col-lg-5 order-md-1">
     
                <div class="testimonial-slider owl-theme owl-carousel">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="faq-img">
                                <img src="{{ asset('assets/img/volunteers/volunteer_1.jpeg') }}" style="height: 300px; width: 100%; object-fit: cover;" alt="Testimonial">
                            </div>
                        </div>

                    </div>
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="faq-img">
                                <img src="{{ asset('assets/img/volunteers/volunteer_2.jpg') }}" style="height: 300px; width: 100%; object-fit: cover;" alt="Testimonial">
                            </div>
                        </div>

                    </div>
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="faq-img">
                                <img src="{{ asset('assets/img/volunteers/volunteer_3.jpeg') }}" style="height: 300px; width: 100%; object-fit: cover;" alt="Testimonial">
                            </div>
                        </div>

                    </div>
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="faq-img">
                                <img src="{{ asset('assets/img/volunteers/volunteer_4.jpeg') }}" style="height: 300px; width: 100%; object-fit: cover;"  alt="Testimonial">
                            </div>
                        </div>

                    </div>
                   



                </div>
                <p class="couresel-caption">Images of some of the volunteers at Teule Kenya</p>

            </div>

    <div class="col-lg-7 order-md-2">
    <div class="contact-area pb-70">
          <div class="container">
          <h4>Apply for Volunteership</h4>
          <hr>

          <div class="row col-md-12 mb-4 volunteer-container">
            <div class="col-md-12"> <h5><b>International volunteers </b></h5></div>

            <div class="col-md-12">jkjkj</div>
         

          </div>
          <div class="row col-md-12 mb-4 volunteer-container">
            <div class="col-md-12"> <h5><b>Kenyan (local) volunteers </b></h5></div>

            <div class="col-md-12">jkjkj</div>
         

          </div>
          <div class="row col-md-12 mb-4 volunteer-container">
            <div class="col-md-12"> <h5><b>International volunteer teams </b></h5></div>

            <div class="col-md-12">jkjkj</div>
         

          </div>
          <div class="row col-md-12 mb-4 volunteer-container">
            <div class="col-md-12"> <h5><b>Kenyan (local) volunteer teams </b></h5></div>

            <div class="col-md-12">jkjkj</div>
         

          </div>
          
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