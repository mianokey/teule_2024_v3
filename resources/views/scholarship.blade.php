@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="page-title-area title-bg-eight">
  <div class="d-table">
    <div class="d-table-cell">
      <div class="container">
        <div class="title-item">
          <h2>TEULE LEADERSHIP ACADEMY-SCHOLARSHIP</h2>
          <ul>
            <li>
              <a href="{{route('home')}}">Home</a>
            </li>
            <li>
              <span>T.L.A SCHOLARSHIP</span>
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
          <h4>THE TEULE LEADERSHIP ACADEMY SCHOLARSHIP PROGRAMME</h4>
          <hr>
          <p style="text-align: justify;">
          The Teule leadership academy is offering sponsorship to all extremely vulnerable children who are gifted academically. Successfull applicants will get 100%, 75%, 50% or 25%  scholarship based on assesment
          
          </br><b><u>STEPS TO APPLY</u></b>
          <ol style="text-align:left">
              
              <li>Collect the scholarship application form from Teule Leadership Academy main office or download through the button below</li>
              <li>Fill the form and attach all required documents.</li>
              <li>Submit the form back to Teule  Leadership Academy main office.</li>
              <li>Please wait for communication regarding shortlisting and the interview.</li>
          </ol>
          </p>
          <a href="{{ asset('uploads/TLA_SCHOLARSHIP_FORM.pdf') }}" download><button class="btn btn-primary btn-custom">Download application form</button></a>
             
          </div>
    </div>

    </div>


    </div>
  </div>
</div>
<x-footer></x-footer>
@endsection