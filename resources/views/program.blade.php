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
<div class="about-area pt-100 pb-70">
  <div class="container">
    <div class="row d-flex justify-content-center align-items-center">

    <div class="col-lg-8 order-md-2">
    <div class="contact-area pb-70">
          <div class="container">
          <h4>Our Program</h4>
          <hr>
            <b>CHOMBO CHA UPENDO</b></br>
                <p style="text-align:justify;">
                  Teule operates a charitable children’s institution called Chombo cha Upendo, which means “Vessel of Love”.  The home is located in Loitokitok, Kenya and provides a loving stable environment
                  for children who are orphaned or have been rescued from abuse, neglect or harmful cultural practices.  With a capacity of up to 70  children, we have children from the ages of 4 to 18+.  The
                  compound has a dorm for the older girls, older boys and a junior dorm.  In addition to providing for the basic needs of the children, our dedicated staff are trained in trauma informed care
                  and behavioral management. Once a child completes primary school they progress to Junior School.  We also have a team of social workers who are responsible for family finding, school visits 
                  and family integration.  Our activities  include outings, community service, life skills workshops and much more.Children are referred to the home by the local  children’s officer for temporary
                  ( less then 6 months) shelter or admitted to the home by a court committee (6 months to 3 years) for residential care. Depending on the case,  a child may be reintegrated back to the home or
                  community before the court committal has expired..  
                  </p>
            <b>COMMUNITY BASED CARE PROGRAM</b></br>
                <p style="text-align:justify;">
                 Teule has always recognized the sacred role of families in raising up children. Through the process of reintegration we have identified the need to strengthen and empower vulnerable families.
                 In 2022, the government of Kenya launched  a national care reform strategy to limit the number of children placements in institutions and reintegrate those already living in institutions,
                 preventing family separation and promoting more child services within the family and  community setting. In the community based program, Teule offers family and community-based services to
                 help children who are leaving residential care and those referred from the community as being vulnerable.This involves support measures and services which strengthen families and prevent child-family
                 separation. It includes access to education and health care, child protection, food security, livelihood empowerment, positive parenting, psychosocial support, economic empowerment, child-headed households
                 support and so on.
                 </p>
 
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